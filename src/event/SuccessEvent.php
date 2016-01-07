<?php
namespace Grasshopper\event;

use Grasshopper\curl\CurlRequest;
use Grasshopper\curl\CurlResponse;

class SuccessEvent extends Event
{
    /**
     * Constructs Event object
     *
     * @param CurlRequest $request
     * @param CurlResponse $response
     */
    public function __construct(CurlRequest $request, CurlResponse $response)
    {
        parent::__construct($request,$response,null);
    }
}