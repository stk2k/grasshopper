<?php
namespace Grasshopper;


class CurlRequest
{
    /** @var string */
    private $url;

    /** @var callable */
    private $complete_callback;

    /** @var callable */
    private $error_callback;

    /** @var resource */
    private $ch;

    /**
     * Constructs grasshopper object
     *
     * @param string $url
     * @param array $options
     */
    public function __construct($url, array $options = array())
    {
        // URL
        $this->url = $url;
        if ( parse_url($url) === false ){
            throw new GrasshopperException('malformed url', Grasshopper::ERROR_MALFORMED_URL);
        }

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
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_USERAGENT =>Grasshopper::DEFAULT_USERAGENT,
            CURLOPT_PROXY => null,
            CURLOPT_HTTPHEADER => array(
                "HTTP/1.0",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Encoding:gzip ,deflate",
                "Accept-Language:en-us;q=0.7,en;q=0.3",
                "Connection:keep-alive",
                "User-Agent:" . Grasshopper::DEFAULT_USERAGENT
            ),
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_BUFFERSIZE => 1024,
            CURLOPT_RETURNTRANSFER => true,

            /* SSL */
            CURLOPT_SSL_VERIFYPEER => false,

            /* redirection */
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
        ];

        $user_curl_options = [
            CURLOPT_USERAGENT => isset($options['user_agent']) ? $options['user_agent'] : NULL,
            CURLOPT_PROXY => isset($options['proxy']) ? $options['proxy'] : NULL,
            CURLOPT_HTTPHEADER => isset($options['http_header']) ? $options['http_header'] : NULL,
            CURLOPT_TIMEOUT => isset($options['timeout']) ? $options['timeout'] : NULL,
            CURLOPT_CONNECTTIMEOUT => isset($options['connect_timeout']) ? $options['connect_timeout'] : NULL,
            CURLOPT_BUFFERSIZE => isset($options['buffer_size']) ? $options['buffer_size'] : NULL,

            /* redirection */
            CURLOPT_MAXREDIRS => isset($options['max_redirs']) ? $options['max_redirs'] : NULL,
            CURLOPT_FOLLOWLOCATION => isset($options['follow_location']) ? $options['follow_location'] : NULL,
        ];

        // merge options between user and defaults
        $real_options = array();
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
            $this->ch = NULL;
        }
    }

    /**
     * Callback when the request is successfully done.
     *
     * @param CurlResponse $response
     */
    public function onRequestSucceeded(CurlResponse $response)
    {
        if ( $this->complete_callback ){
            call_user_func_array( $this->complete_callback, [$response] );
        }
    }

    /**
     * Callback when the request has failed.
     *
     * @param CurlError $error
     */
    public function onRequestFailed(CurlError $error)
    {
        if ( $this->error_callback ){
            call_user_func_array( $this->error_callback, [$error] );
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