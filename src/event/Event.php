<?php
namespace Grasshopper\event;

use Grasshopper\curl\CurlRequest;

class Event
{
    /** @var CurlRequest  */
    private $request;

    /**
     * Constructs Event object
     *
     * @param CurlRequest $request
     */
    public function __construct($request)
    {
        $this->requset = $request;
    }

    /**
     * get request object
     */
    public function getRequest()
    {
        return $this->request;
    }
}