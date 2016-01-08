<?php
namespace Grasshopper\curl;


class CurlError
{
    /** @var  int */
    private $errno;

    /** @var  string */
    private $curl_function;

    /** @var  string */
    private $errmsg;

    /**
     * Constructs CurlError object
     *
     * @param int $errno
     * @param string $curl_function
     */
    public function __construct($errno, $curl_function)
    {
        $this->errno = $errno;
        $this->curl_function = $curl_function;
        $this->errmsg = curl_strerror($errno) . ': ' . $curl_function;;
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
     * Get function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->curl_function;
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