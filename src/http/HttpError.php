<?php
namespace Grasshopper\http;

use Grasshopper\Error;

class HttpError implements Error
{
    /** @var  int */
    private $errno;

    /** @var  string */
    private $message;

    /**
     * Constructs CurlError object
     *
     * @param int $errno
     * @param string $message
     */
    public function __construct($errno, $message)
    {
        $this->errno = $errno;
        $this->message = $message;
    }

    /**
     * Get error number
     *
     * @return int
     */
    public function getNo()
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
        return $this->message;
    }

}