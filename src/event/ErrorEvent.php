<?php
namespace Grasshopper\event;

use Grasshopper\Error;
use Grasshopper\curl\CurlRequest;
use Grasshopper\curl\CurlResponse;

class ErrorEvent extends Event
{
    /** @var Error */
    private $error;

    /** @var CurlResponse|null */
    private $response;

    /**
     * Constructs Event object
     *
     * @param CurlRequest $request
     * @param Error $error
     * @param CurlResponse $response
     */
    public function __construct(CurlRequest $request, Error $error, $response = null)
    {
        parent::__construct($request);

        $this->error = $error;
        $this->response = $response;
    }

    /**
     * get error object
     *
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * get response object
     */
    public function getResponse()
    {
        return $this->response;
    }
}