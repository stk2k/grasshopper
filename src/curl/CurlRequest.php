<?php
namespace Grasshopper\curl;

use Grasshopper\Grasshopper;
use Grasshopper\exception\GrasshopperException;
use Grasshopper\HttpPostRequest;
use Grasshopper\event\SuccessEvent;
use Grasshopper\event\ErrorEvent;

class CurlRequest
{
    /** @var string */
    private $method;

    /** @var string */
    private $url;

    /** @var callable */
    private $complete_callback;

    /** @var callable */
    private $error_callback;

    /** @var resource */
    private $ch;

    /**
     * Constructs cURL request object
     *
     * @param string $method
     * @param string $url
     * @param array $options
     */
    public function __construct($method, $url, array $options = [])
    {
        // method
        $this->method = $method;

        // URL
        $this->url = $url;
        if ( parse_url($url) === false ){
            throw new GrasshopperException('malformed url', Grasshopper::ERROR_MALFORMED_URL);
        }

        // callbacks
        $this->complete_callback = isset($options['complete']) ? $options['complete'] : null;
        $this->error_callback = isset($options['error']) ? $options['error'] : null;

        if ( $this->complete_callback && !is_callable($this->complete_callback) ){
            throw new GrasshopperException('invalid complete callback', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }
        if ( $this->error_callback && !is_callable($this->error_callback) ){
            throw new GrasshopperException('invalid error callback', Grasshopper::ERROR_INVALID_REQUEST_PARAMETER);
        }

        // options
        $dafaults = [
            /* user NOT customizable fields by options parameter */
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,

            /* user customizable fields by options parameter */
            CURLOPT_USERAGENT => Grasshopper::DEFAULT_USERAGENT,
            CURLOPT_PROXY => null,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
            CURLOPT_HTTPHEADER => (new CurlRequestHeader)->compile(),
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_BUFFERSIZE => 1024,

            /* SSL */
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,

            /* HTTP/POST */
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => '',

            /* redirection */
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
        ];

        $user_curl_options = [
            /* user customizable fields by options parameter */
            CURLOPT_USERAGENT => isset($options['user_agent']) ? $options['user_agent'] : null,
            CURLOPT_PROXY => isset($options['proxy']) ? $options['proxy'] : null,
            CURLOPT_HTTP_VERSION => isset($options['http_version']) ? $options['http_version'] : null,
            CURLOPT_HTTPHEADER => isset($options['http_header']) ? $options['http_header'] : null,
            CURLOPT_TIMEOUT => isset($options['timeout']) ? $options['timeout'] : null,
            CURLOPT_CONNECTTIMEOUT => isset($options['connect_timeout']) ? $options['connect_timeout'] : null,
            CURLOPT_BUFFERSIZE => isset($options['buffer_size']) ? $options['buffer_size'] : null,

            /* HTTP/POST */
            CURLOPT_POST => ($this instanceof HttpPostRequest),
            CURLOPT_POSTFIELDS => isset($options['post_fields']) ? $options['post_fields'] : null,

            /* redirection */
            CURLOPT_MAXREDIRS => isset($options['max_redirs']) ? $options['max_redirs'] : null,
            CURLOPT_FOLLOWLOCATION => isset($options['follow_location']) ? $options['follow_location'] : null,
            CURLOPT_AUTOREFERER => isset($options['auto_referer']) ? $options['auto_referer'] : null,
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
        $real_options = [];
        foreach($dafaults as $k => $v){
            $user_val = isset($user_curl_options[$k]) ? $user_curl_options[$k] : null;
            $real_options[$k] = $user_val ? $user_val : $v;
        }

        // init curl handle
        $this->ch = curl_init();
        if ( !$this->ch ){
            throw new GrasshopperException('curl_init failed',Grasshopper::ERROR_INIT);
        }

        // set options to curl handle
        curl_setopt_array($this->ch, $real_options);
    }

    /**
     * Get URL
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * Get cURL handle
     */
    public function getCurlHandle(){
        return $this->ch;
    }

    /**
     * Detach from cURL multi handle
     *
     * @param resource $cm_handle
     */
    public function detach($cm_handle){
        if ( $this->ch ){
            curl_multi_remove_handle( $cm_handle, $this->ch );
        }
    }

    /**
     * close cURL handle and memory file handle
     */
    public function close(){
        if ( $this->ch ){
            curl_close($this->ch);
            $this->ch = null;
        }
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
     * string conversion
     */
    public function __toString()
    {
        return 'CurlRequest(' . $this->url . ')';
    }
}