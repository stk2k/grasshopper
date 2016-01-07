<?php
namespace Grasshopper\event;

use Grasshopper\curl\CurlRequest;
use Grasshopper\curl\CurlResponse;
use Grasshopper\curl\CurlError;

class Event
{
    /** @var CurlRequest  */
    private $request;

    /** @var CurlResponse|null */
    private $response;

    /** @var CurlError|null */
    private $error;

    /**
     * Constructs Event object
     *
     * @param CurlRequest $request
     * @param CurlResponse $response
     * @param CurlError $error
     */
    public function __construct($request, $response, $error)
    {
        $this->requset = $request;
        $this->response = $response;
        $this->error = $error;
    }

    /**
     * get request object
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * get response object
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * get error object
     */
    public function getError()
    {
        return $this->error;
    }
}