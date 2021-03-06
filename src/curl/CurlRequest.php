<?php
namespace Grasshopper\curl;

use Grasshopper\Grasshopper;
use Grasshopper\exception\GrasshopperException;
use Grasshopper\HttpPostRequest;
use Grasshopper\event\SuccessEvent;
use Grasshopper\event\ErrorEvent;
use Grasshopper\debug\CurlDebug;

abstract class CurlRequest
{
    const DEFAULT_TIMEOUT = 60;
    const DEFAULT_CONNECTTIMEOUT = 60;

    /** @var string */
    private $url;

    /** @var callable */
    private $complete_callback;

    /** @var callable */
    private $error_callback;

    /** @var resource */
    private $tmpfile;

    /** @var int */
    private $max_download_size;

    /** @var array */
    private $options;

    /**
     * Constructs cURL request object
     *
     * @param string $url
     * @param array $options
     */
    public function __construct($url, $options = null)
    {
        if (!$options){
            $options = array();
        }
        
        // URL
        $this->url = $url;
        if ( parse_url($url) === false ){
            throw new GrasshopperException('malformed url', Grasshopper::ERROR_MALFORMED_URL);
        }

        // method
        $this->max_download_size = isset($options['max_download_size']) ? $options['max_download_size'] : Grasshopper::DEFAULT_MAX_DOWNLOAD_SIZE;

        // callbacks
        $this->complete_callback = isset($options['complete']) ? $options['complete'] : null;
        $this->error_callback = isset($options['error']) ? $options['error'] : null;

        if ( $this->complete_callback && !is_callable($this->complete_callback) ){
            throw new GrasshopperException('invalid complete callback', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }
        if ( $this->error_callback && !is_callable($this->error_callback) ){
            throw new GrasshopperException('invalid error callback', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }

        $this->tmpfile = tmpfile();

        // options
        $dafaults = [
            /* user NOT customizable fields by options parameter */
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_PROGRESSFUNCTION => function($resource, $down_size, $downloaded, $upload_size, $uploaded){
                return $downloaded > $this->max_download_size ? 1 : 0;
            },
            CURLINFO_HEADER_OUT => true,

            /* user customizable fields by options parameter */
            CURLOPT_USERAGENT => Grasshopper::DEFAULT_USERAGENT,
            CURLOPT_PROXY => null,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
            CURLOPT_HTTPHEADER => '',
            CURLOPT_BUFFERSIZE => Grasshopper::DEFAULT_BUFFER_SIZE,

            /* SSL */
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,

            /* HTTP/POST */
            CURLOPT_POSTFIELDS => '',

            /* redirection */
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            
            /* callback functions */
            CURLOPT_HEADERFUNCTION => null,
            CURLOPT_WRITEFUNCTION => null,

            /* file */
            CURLOPT_FILE => $this->tmpfile,
            CURLOPT_NOPROGRESS => false,
            
            /* Authentication */
            CURLOPT_USERPWD => null,
        ];

        $user_curl_options = [
            /* user customizable fields by options parameter */
            CURLOPT_USERAGENT => isset($options['user_agent']) ? $options['user_agent'] : null,
            CURLOPT_PROXY => isset($options['proxy']) ? $options['proxy'] : null,
            CURLOPT_HTTP_VERSION => isset($options['http_version']) ? $options['http_version'] : CURL_HTTP_VERSION_NONE ,
            CURLOPT_HTTPHEADER => isset($options['http_header']) ? $options['http_header'] : null,
            CURLOPT_BUFFERSIZE => isset($options['buffer_size']) ? $options['buffer_size'] : null,

            /* HTTP/POST */
            CURLOPT_POST => ($this instanceof HttpPostRequest),
            CURLOPT_POSTFIELDS => isset($options['post_fields']) ? $options['post_fields'] : null,

            /* redirection */
            CURLOPT_MAXREDIRS => isset($options['max_redirs']) ? $options['max_redirs'] : null,
            CURLOPT_FOLLOWLOCATION => isset($options['follow_location']) ? $options['follow_location'] : null,
            CURLOPT_AUTOREFERER => isset($options['auto_referer']) ? $options['auto_referer'] : null,
    
            /* header functions */
            CURLOPT_HEADERFUNCTION => isset($options['header_function']) ? $options['header_function'] : null,
            CURLOPT_WRITEFUNCTION => isset($options['write_function']) ? $options['write_function'] : null,
    
            /* Authentication */
            CURLOPT_USERPWD => isset($options['user_password']) ? $options['user_password'] : null,
        ];

        // HTTP header
        $user_http_header = isset($options['http_header']) ? $options['http_header'] : new CurlRequestHeader;
        if ( is_array($user_http_header) ){
            $user_http_header = new CurlRequestHeader($user_http_header);
        }
        elseif ( !($user_http_header instanceof CurlRequestHeader) ){
            throw new GrasshopperException('invalid http header', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }
        $user_curl_options[CURLOPT_HTTPHEADER] = $user_http_header->compile();

        // merge options between user and defaults
        $real_options = array();
        foreach($dafaults as $k => $v){
            $user_val = isset($user_curl_options[$k]) ? $user_curl_options[$k] : null;
            $real_options[$k] = $user_val !== null ? $user_val : $v;
        }

        // debug
        $real_options[CURLOPT_VERBOSE] = isset($options['verbose']) ? $options['verbose'] : false;
        if (defined('STDERR')){
            $real_options[CURLOPT_STDERR] = isset($options['stderr']) ? $options['stderr'] : STDERR;
        }

        // set timeout
        if ( isset($options['timeout']) ){
            $real_options[CURLOPT_TIMEOUT] = $options['timeout'];
        }
        elseif ( isset($options['timeout_ms']) ){
            $real_options[CURLOPT_TIMEOUT_MS] = $options['timeout_ms'];
        }
        else{
            $real_options[CURLOPT_TIMEOUT] = self::DEFAULT_TIMEOUT;
        }

        // set connect timeout
        if ( isset($options['connect_timeout']) ){
            $real_options[CURLOPT_CONNECTTIMEOUT] = $options['connect_timeout'];
        }
        elseif ( isset($options['connect_timeout_ms']) ){
            $real_options[CURLOPT_CONNECTTIMEOUT_MS] = $options['connect_timeout_ms'];
        }
        else{
            $real_options[CURLOPT_CONNECTTIMEOUT] = self::DEFAULT_CONNECTTIMEOUT;
        }
    
        // unset unnecessary options
        $unnecessary_check_functions = array(
            CURLOPT_HEADERFUNCTION => function($item) { return !is_callable($item); },
            CURLOPT_WRITEFUNCTION => function($item) { return !is_callable($item); },
        );
        foreach($unnecessary_check_functions as $key => $unnecessary_check){
            if ( array_key_exists($key,$real_options) && $unnecessary_check($real_options[$key]) ){
                unset($real_options[$key]);
            }
        }

        $this->options = $this->overrideOptions( $real_options );
    }
    
    /**
     * Override curl options
     *
     * @param array $options
     *
     * @return array
     */
    abstract protected function overrideOptions($options);

    /**
     * Check if verbose
     *
     * @return boolean
     */
    public function isVerbose(){
        return isset($this->options[CURLOPT_VERBOSE]) ? $this->options[CURLOPT_VERBOSE] : false;
    }

    /**
     * Get method
     *
     * @return boolean
     */
    public function getMethod(){
        return isset($this->options[CURLOPT_CUSTOMREQUEST]) ? $this->options[CURLOPT_CUSTOMREQUEST] : '';
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * Get timeout
     *
     * @param boolean $in_millisec
     *
     * @return integer
     */
    public function getTimeout( $in_millisec = false ){
        if ( $in_millisec ){
            return isset($this->options[CURLOPT_TIMEOUT_MS]) ? $this->options[CURLOPT_TIMEOUT_MS] : -1;
        }
        return isset($this->options[CURLOPT_TIMEOUT]) ? $this->options[CURLOPT_TIMEOUT] : -1;
    }

    /**
     * Get connect timeout
     *
     * @param boolean $in_millisec
     *
     * @return integer
     */
    public function getConnectTimeout( $in_millisec = false ){
        if ( $in_millisec ){
            return isset($this->options[CURLOPT_CONNECTTIMEOUT_MS]) ? $this->options[CURLOPT_CONNECTTIMEOUT_MS] : -1;
        }
        return isset($this->options[CURLOPT_CONNECTTIMEOUT]) ? $this->options[CURLOPT_CONNECTTIMEOUT] : -1;
    }

    /**
     * Get cURL options
     *
     * @return array
     */
    public function getOptions(){
        return $this->options;
    }

    /**
     * Get file handle
     *
     * @return resource
     */
    public function getFileHandle(){
        return $this->tmpfile;
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
     * Callback when the request is successfully done.
     *
     * @param SuccessEvent $event
     */
    public function onRequestSucceeded(SuccessEvent $event)
    {
        if ( $this->complete_callback ){
            call_user_func_array( $this->complete_callback, [$event] );
        }
    }

    /**
     * Callback when the request has failed.
     *
     * @param ErrorEvent $event
     */
    public function onRequestFailed(ErrorEvent $event)
    {
        if ( $this->error_callback ){
            call_user_func_array( $this->error_callback, [$event] );
        }
    }

    /**
     * Show options
     */
    public function printOptions()
    {
        CurlDebug::printOptions($this->options);
    }

    /**
     * string conversion
     */
    public function __toString()
    {
        return 'CurlRequest(' . $this->url . ')';
    }
}