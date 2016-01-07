<?php
namespace Grasshopper\curl;


class CurlError
{
    /** @var string */
    private $request_url;

    /** @var  int */
    private $errno;

    /** @var  string */
    private $errmsg;

    /**
     * Constructs CurlError object
     *
     * @param string $request_url
     * @param resource $curl_handle
     */
    public function __construct($request_url, $curl_handle)
    {
        $this->request_url = $request_url;
        $this->errno = curl_errno($curl_handle);
        $this->errmsg = curl_strerror($this->errno);
    }

    /**
     * Get URL
     */
    public function getRequestUrl(){
        return $this->request_url;
    }

    /**
     * Get error number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->errno;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->errmsg;
    }
}