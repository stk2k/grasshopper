<?php
namespace Grasshopper;

class Grasshopper
{
    const MEMFILE_PROTOCOL_NAME = 'grasshopper';

    const ERROR_INIT = 0;
    const ERROR_MULTI_INIT = 1;
    const ERROR_CLOSED = 2;
    const ERROR_MULTI_EXEC = 5;
    const ERROR_INFO_READ = 6;
    const ERROR_FIND_REQUEST = 7;
    const ERROR_INVALID_REQUEST_PARAMETER = 8;
    const ERROR_MALFORMED_URL = 9;

    const DEFAULT_USERAGENT = 'Grasshopper';
    const DEFAULT_USLEEP = 100;
    const DEFAULT_WAIT_TMEOUT = 60;

    /** @var CurlRequest[] */
    private $requests;

    /** @var resource */
    private $mh;

    /** @var callable */
    private $complete_callback;

    /** @var callable */
    private $error_callback;

    /**
     * Constructs grasshopper object
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        // callbacks
        $this->complete_callback = isset($options['complete']) ? $options['complete'] : NULL;
        $this->error_callback = isset($options['error']) ? $options['error'] : NULL;

        if ( $this->complete_callback && !is_callable($this->complete_callback) ){
            throw new GrasshopperException('invalid complete callback', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }
        if ( $this->error_callback && !is_callable($this->error_callback) ){
            throw new GrasshopperException('invalid error callback', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }

        // options
        $dafaults = [
            CURLMOPT_PIPELINING => 1,
            CURLMOPT_MAXCONNECTS => 10,
        ];

        $user_mcurl_options = [
            CURLMOPT_PIPELINING => isset($options['pipelining']) ? $options['pipelining'] : NULL,
            CURLMOPT_MAXCONNECTS => isset($options['max_connects']) ? $options['max_connects'] : NULL,
        ];

        // merge options between user and defaults
        $real_options = array();
        foreach($dafaults as $k => $v){
            $user_val = isset($user_mcurl_options[$k]) ? $user_mcurl_options[$k] : null;
            $real_options[$k] = $user_val ? $user_val : $v;
        }

        // init curl multi handle
        $this->mh = curl_multi_init();
        if ( !$this->mh ){
            throw new GrasshopperException('curl_multi_init failed',self::ERROR_MULTI_INIT);
        }

        // set options to curl multi handle
        curl_multi_setopt( $this->mh, CURLMOPT_PIPELINING, $real_options[CURLMOPT_PIPELINING] );
        curl_multi_setopt( $this->mh, CURLMOPT_MAXCONNECTS, $real_options[CURLMOPT_MAXCONNECTS] );
    }

    /**
     * Destructs grasshopper object
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Get requests
     *
     * @return CurlRequest[]
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Add request
     *
     * @param CurlRequest $request
     *
     * @return resource
     */
    public function addRequest(CurlRequest $request)
    {
        if ( !$this->mh ){
            throw new GrasshopperException('curl multi handle is already closed',Grasshopper::ERROR_CLOSED);
        }
        $this->requests[] = $request;
        curl_multi_add_handle($this->mh, $request->getCurlHandle());
    }

    /**
     * Add requests
     *
     * @param CurlRequest[] $requests
     *
     * @return resource
     */
    public function addRequests(array $requests)
    {
        if ( !$this->mh ){
            throw new GrasshopperException('curl multi handle is already closed',Grasshopper::ERROR_CLOSED);
        }
        foreach ( $requests as $req) {
            $this->requests[] = $req;
            curl_multi_add_handle($this->mh, $req->getCurlHandle());
        }
    }

    /**
     * wait for all responses
     *
     * @param int $usleep
     * @param int $timeout
     *
     * @return array
     */
    public function waitForAll($usleep = self::DEFAULT_USLEEP, $timeout = self::DEFAULT_WAIT_TMEOUT)
    {
        $wait_left = (float)($timeout) * 1000;
        if ( !$this->mh ){
            throw new GrasshopperException('curl multi handle is already closed',Grasshopper::ERROR_CLOSED);
        }
        $result = array();

        // wait for all responses
        $start = microtime(true);
        curl_multi_select($this->mh, $timeout);
        $wait_left -= (microtime(true) - $start);

        do {
            $stat = curl_multi_exec($this->mh, $running);
            if ( $running ){
                if ( $wait_left < $usleep ){
                    throw new TimeoutException();
                }
                usleep($usleep);
                $wait_left -= $usleep;
                continue;
            }
        } while ($stat === CURLM_CALL_MULTI_PERFORM || $running);

        if ( $stat !== CURLM_OK ) {
            $errmsg = curl_multi_strerror($stat);
            throw new GrasshopperException('curl_multi_exec failed:' . $errmsg, self::ERROR_MULTI_EXEC);
        }

        // read each response
        do {
            $res = curl_multi_info_read($this->mh, $remains);
            if ( !$res ) {
                if ( $wait_left < $usleep ){
                    throw new TimeoutException();
                }
                usleep($usleep);
                $wait_left -= $usleep;
                $remains = 1;
                continue;
            }

            /** @var resource $ch */
            $ch = $res['handle'];

            /** @var CurlRequest $req */
            $req = $this->findRequestFromHandle($ch);

            /** @var string $url */
            $request_url = $req->getUrl();

            if ( $res['result'] !== CURLE_OK ){
                goto REQUEST_FAILED;
            }

            $info = curl_getinfo($ch);
            if ( $info === false ){
                goto REQUEST_FAILED;
            }
            $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            if ( $effective_url === false ){
                goto REQUEST_FAILED;
            }
            $info['effective_url'] = $effective_url;

            $content = curl_multi_getcontent($ch);
            if ( $content === false ){
                goto REQUEST_FAILED;
            }

REQUEST_SUCCEEDED:
            {
                $response = new CurlResponse($request_url, $info, $content);
                $req->onRequestSucceeded($response);
                if ( $this->complete_callback ){
                    call_user_func_array( $this->complete_callback, [$response] );
                }
                $result[$request_url] = $response;
                goto REQUEST_FINISH;
            }

REQUEST_FAILED:
            {
                $error = new CurlError($request_url, $ch);
                $req->onRequestFailed($error);
                if ( $this->error_callback ){
                    call_user_func_array( $this->error_callback, [$error] );
                }
                $result[$request_url] = $error;
                goto REQUEST_FINISH;
            }

REQUEST_FINISH:
            {
                curl_multi_remove_handle($this->mh, $ch);
                $req->close();
            }

        } while ($remains);

        return $result;
    }

    /**
     * close cURL handles
     */
    public function close()
    {
        if ( $this->requests ){
            foreach( $this->requests as $req ){
                $req->detach( $this->mh );
                $req->close();
            }
            $this->requests = NULL;
        }
        if ( $this->mh ){
            curl_multi_close( $this->mh );
            $this->mh = NULL;
        }
    }

    /**
     * Find request from cURL handle
     *
     * @param resource $curl_handle
     *
     * @return CurlRequest
     */
    private function findRequestFromHandle($curl_handle)
    {
        foreach($this->requests as $req){
            if ( $req->getCurlHandle() === $curl_handle ){
                return $req;
            }
        }
        throw new GrasshopperException('could not find request from handle', self::ERROR_FIND_REQUEST);
    }

}