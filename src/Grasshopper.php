<?php
namespace Grasshopper;

use Grasshopper\exception\GrasshopperException;
use Grasshopper\exception\TimeoutException;
use Grasshopper\event\SuccessEvent;
use Grasshopper\event\ErrorEvent;
use Grasshopper\curl\CurlRequest;
use Grasshopper\curl\CurlResponse;
use Grasshopper\curl\CurlError;

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
    const DEFAULT_USLEEP = 20;
    const DEFAULT_WAIT_TMEOUT = 6000;
    const DEFAULT_MAX_DOWNLOAD_SIZE = 104857600;       // 100MB
    const DEFAULT_BUFFER_SIZE = 1048576;       // 1MB

    /** @var CurlRequest[] */
    private $requests;

    /** @var resource */
    private $mh;

    /** @var callable */
    private $complete_callback;

    /** @var callable */
    private $error_callback;

    /** @var int */
    private $max_download_size;

    /**
     * Constructs grasshopper object
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        // callbacks
        $this->complete_callback = isset($options['complete']) ? $options['complete'] : null;
        $this->error_callback = isset($options['error']) ? $options['error'] : null;
        $this->max_download_size = isset($options['max_download_size']) ? $options['max_download_size'] : self::DEFAULT_MAX_DOWNLOAD_SIZE;

        if ( $this->complete_callback && !is_callable($this->complete_callback) ){
            throw new GrasshopperException('invalid complete callback', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }
        if ( $this->error_callback && !is_callable($this->error_callback) ){
            throw new GrasshopperException('invalid error callback', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }

        // options
        $dafaults = [
            /* user customizable fields by options parameter */
            CURLMOPT_PIPELINING => 1,
            CURLMOPT_MAXCONNECTS => 10,
        ];

        $user_mcurl_options = [
            /* user customizable fields by options parameter */
            CURLMOPT_PIPELINING => isset($options['pipelining']) ? $options['pipelining'] : null,
            CURLMOPT_MAXCONNECTS => isset($options['max_connects']) ? $options['max_connects'] : null,
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
        foreach ( $requests as $request) {
            $this->requests[] = $request;
            curl_multi_add_handle($this->mh, $request->getCurlHandle());
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
        $result = [];

        do {
            $start = microtime(true);
            curl_multi_select($this->mh);
            $wait_left -= (microtime(true) - $start);

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

            /** @var CurlRequest $request */
            $request = $this->findRequestFromHandle($ch);

            /** @var string $url */
            $request_url = $request->getUrl();

            if ( $res['result'] !== CURLE_OK ){
                $errno = $res['result'];
                $function = 'curl_multi_info_read';
                goto REQUEST_FAILED;
            }

            $info = curl_getinfo($ch);
            if ( $info === false ){
                $errno = curl_errno($ch);
                $function = 'curl_getinfo';
                goto REQUEST_FAILED;
            }
            $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            if ( $effective_url === false ){
                $errno = curl_errno($ch);
                $function = 'curl_getinfo';
                goto REQUEST_FAILED;
            }
            $info['effective_url'] = $effective_url;
/*
            $content = curl_multi_getcontent($ch);
            if ( $content === false ){
                $errno = curl_errno($ch);
                $function = 'curl_multi_getcontent';
                goto REQUEST_FAILED;
            }
*/
            $fp = $request->getFileHandle();
            fseek($fp, 0);
            $content =  fread($fp, $this->max_download_size);
            fclose($fp);

REQUEST_SUCCEEDED:
            {
                $response = new CurlResponse($info, $content);
                $event = new SuccessEvent($request, $response);
                $request->onRequestSucceeded($event);
                if ( $this->complete_callback ){
                    call_user_func_array( $this->complete_callback, [$event] );
                }
                $result[$request_url] = $event;
                goto REQUEST_FINISH;
            }

REQUEST_FAILED:
            {
                $error = new CurlError($errno, $function);
                $event = new ErrorEvent($request, $error);
                $request->onRequestFailed($event);
                if ( $this->error_callback ){
                    call_user_func_array( $this->error_callback, [$event] );
                }
                $result[$request_url] = $event;
                goto REQUEST_FINISH;
            }

REQUEST_FINISH:
            {
                curl_multi_remove_handle($this->mh, $ch);
                $request->close();
            }

        } while ($remains);

        return $result;
    }

    /**
     * Get max download size
     */
    public function getMaxDownloadSize()
    {
        return $this->max_download_size;
    }

    /**
     * close cURL handles
     */
    public function close()
    {
        if ( $this->requests ){
            foreach( $this->requests as $request ){
                $request->detach( $this->mh );
                $request->close();
            }
            $this->requests = null;
        }
        if ( $this->mh ){
            curl_multi_close( $this->mh );
            $this->mh = null;
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
        foreach($this->requests as $request){
            if ( $request->getCurlHandle() === $curl_handle ){
                return $request;
            }
        }
        throw new GrasshopperException('could not find request from handle', self::ERROR_FIND_REQUEST);
    }

}