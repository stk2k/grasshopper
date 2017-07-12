<?php
namespace Grasshopper;

use Grasshopper\exception\GrasshopperException;
use Grasshopper\exception\TimeoutException;
use Grasshopper\exception\UserCancelException;
use Grasshopper\exception\DeflateException;
use Grasshopper\event\SuccessEvent;
use Grasshopper\event\ErrorEvent;
use Grasshopper\curl\CurlRequest;
use Grasshopper\curl\CurlResponse;
use Grasshopper\curl\CurlError;
use Grasshopper\curl\CurlMultiHandle;
use Grasshopper\curl\CurlHandlePool;
use Grasshopper\http\HttpError;

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
    const ERROR_SETOPTION = 10;
    const ERROR_SETOPTIONS = 11;
    const ERROR_M_SETOPTION = 12;
    const ERROR_M_SETOPTIONS = 13;
    const ERROR_INVALID_OPTION = 14;

    const DEFAULT_POOL_SIZE = 10;
    const DEFAULT_USERAGENT = 'Grasshopper';
    const DEFAULT_SLEEP_WAIT = 20;
    const DEFAULT_WAIT_TMEOUT = 6000;
    const DEFAULT_MAX_DOWNLOAD_SIZE = 104857600;       // 100MB
    const DEFAULT_BUFFER_SIZE = 1048576;       // 1MB

    /** @var CurlRequest[] */
    private $requests;

    /** @var callable */
    private $complete_callback;

    /** @var callable */
    private $error_callback;

    /** @var int */
    private $max_download_size;

    /** @var array */
    private $options;

    /** @var CurlHandlePool */
    private $pool;

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
            CURLMOPT_MAXCONNECTS => 10,
        ];

        $user_mcurl_options = [
            /* user customizable fields by options parameter */
            CURLMOPT_MAXCONNECTS => isset($options['max_connects']) ? $options['max_connects'] : null,
        ];
        
        if ( defined('CURLMOPT_PIPELINING') ){
            $dafaults[CURLMOPT_PIPELINING] = 1;
            $user_mcurl_options[CURLMOPT_PIPELINING] = isset($options['pipelining']) ? $options['pipelining'] : null;
        }

        // merge options between user and defaults
        $real_options = array();
        foreach($dafaults as $k => $v){
            $user_val = isset($user_mcurl_options[$k]) ? $user_mcurl_options[$k] : null;
            $real_options[$k] = $user_val ? $user_val : $v;
        }

        $this->options = $real_options;

        $pool_size = isset($options['pool_size']) ? $options['pool_size'] : self::DEFAULT_POOL_SIZE;
        $this->pool = new CurlHandlePool($pool_size);

        $this->requests = array();
    }

    /**
     * Reset object
     *
     * @return Grasshopper
     */
    public function reset()
    {
        $this->requests = array();
        return $this;
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
     * Get max download size
     *
     * @return integer
     */
    public function getMaxDownloadSize()
    {
        return $this->max_download_size;
    }

    /**
     * Add request
     *
     * @param string $url
     * @param array $options
     *
     * @return Grasshopper
     *
     * @throws \InvalidArgumentException
     */
    public function addRequest($url, $options = array())
    {
        $request = new HttpGetRequest($url, $options);
        $this->requests[] = $request;
        return $this;
    }

    /**
     * Add requests
     *
     * @param CurlRequest[] $requests
     *
     * @return Grasshopper
     */
    public function addRequests(array $requests)
    {
        foreach ( $requests as $request) {
            $this->requests[] = $request;
        }
        return $this;
    }

    /**
     * wait for all responses
     *
     * $wait_function indicates like this:
     *
     * function wait_func($total_wait, $function){
     *     // $total_wait: total wait time in msec
     *     // $function: function name which needs wait
     *     // return value: if you want to break waitForAll, return true.
     *     if ( $total_wait > 2000 )    return true;   // will cancel waitForAll(throws UserCancelException)
     *     usleep(20);
     *     return false;    // continue execution of waitForAll
     * }
     *
     * @param boolean $bulk_set_options
     * @param callable $wait_function    user defined wait function.if this set to null, default wait ant default
     *                                   timeout will be applied.
     *
     * @return array
     *
     * @throws UserCancelException
     */
    public function waitForAll($bulk_set_options = true, $wait_function = null)
    {
        $mh = new CurlMultiHandle();
    
        if ( $bulk_set_options ){
            $mh->setOptions($this->options);
        }
        else{
            foreach( $this->options as $key => $value ){
                $mh->setOption( $key, $value );
            }
        }

        foreach( $this->requests as $req ){
            $cho = $this->pool->acquireObject();
            $cho->setRequest($req, $bulk_set_options);
            $mh->addHandle($cho);
        }

        $result = [];

        $total_wait = 0;

        do {
            $mh->select();

            $stat = $mh->execute($running);
            if ( $running ){
                $start = microtime(true);
                if ( $wait_function && is_callable($wait_function) ){
                    $canceled = call_user_func_array($wait_function, [$total_wait, 'curl_multi_exec']);
                    if ( $canceled ){
                        throw new UserCancelException();
                    }
                }
                else{
                    // default wait
                    if ( $total_wait + self::DEFAULT_SLEEP_WAIT > self::DEFAULT_WAIT_TMEOUT ){
                        throw new TimeoutException();
                    }
                    usleep(self::DEFAULT_SLEEP_WAIT);
                }
                $total_wait += (microtime(true) - $start);
                continue;
            }
        } while ($stat === CURLM_CALL_MULTI_PERFORM || $running);

        if ( $stat !== CURLM_OK ) {
            $errmsg = curl_multi_strerror($stat);
            throw new GrasshopperException('curl_multi_exec failed:' . $errmsg, self::ERROR_MULTI_EXEC);
        }

        // read each response
        do {
            $res = $mh->getInfo($remains);
            if ( !$res ) {
                $start = microtime(true);
                if ( $wait_function && is_callable($wait_function) ){
                    $canceled = call_user_func_array($wait_function, [$total_wait, 'curl_multi_info_read']);
                    if ( $canceled ){
                        throw new UserCancelException();
                    }
                }
                else{
                    // default wait
                    if ( $total_wait + self::DEFAULT_SLEEP_WAIT > self::DEFAULT_WAIT_TMEOUT ){
                        throw new TimeoutException();
                    }
                    usleep(self::DEFAULT_SLEEP_WAIT);
                }
                $total_wait += (microtime(true) - $start);
                $remains = 1;
                continue;
            }

            $response = null;

            /** @var resource $ch */
            $ch = $res['handle'];

            // find cURL handle object
            $cho = $this->pool->findObject($ch);

            /** @var CurlRequest $request */
            $request = $cho->getRequest();

            /** @var string $url */
            $request_url = $request->getUrl();

            if ( $res['result'] !== CURLE_OK ){
                $errno = $res['result'];
                $function = 'curl_multi_info_read';
                $error = new CurlError($errno, $function);
                goto REQUEST_FAILED;
            }

            $info = curl_getinfo($ch);
            if ( $info === false ){
                $errno = curl_errno($ch);
                $function = 'curl_getinfo';
                $error = new CurlError($errno, $function);
                goto REQUEST_FAILED;
            }
            $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            if ( $effective_url === false ){
                $errno = curl_errno($ch);
                $function = 'curl_getinfo';
                $error = new CurlError($errno, $function);
                goto REQUEST_FAILED;
            }
            $info['effective_url'] = $effective_url;

            $fp = $request->getFileHandle();
            fseek($fp, 0);
            $content =  fread($fp, $this->max_download_size);
            fclose($fp);

REQUEST_SUCCEEDED:
            {
                $response = null;
                try{
                    $response = new CurlResponse($info, $content);
                }
                catch( DeflateException $ex ){
                    $error = new HttpError(0, 'failed to deflate');
                    goto REQUEST_FAILED;
                }
                $event = new SuccessEvent($request, $response);
                // callback
                $request->onRequestSucceeded($event);
                if ( $this->complete_callback ){
                    call_user_func_array( $this->complete_callback, [$event] );
                }
                $result["$request_url"] = $event;
                goto REQUEST_FINISH;
            }

REQUEST_FAILED:
            {
                $event = new ErrorEvent($request, $error, $response);
                // callback
                $request->onRequestFailed($event);
                if ( $this->error_callback ){
                    call_user_func_array( $this->error_callback, [$event] );
                }
                $result["$request_url"] = $event;
                goto REQUEST_FINISH;
            }

REQUEST_FINISH:
            {
                $mh->removeHandle( $cho );
                $this->pool->releaseObject( $cho );
            }

        } while ($remains);

        $mh->close();

        return $result;
    }

}