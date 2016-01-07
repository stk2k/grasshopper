<?php
namespace Grasshopper\event;

use Grasshopper\curl\CurlRequest;
use Grasshopper\curl\CurlError;

class ErrorEvent extends Event
{
    /**
     * Constructs Event object
     *
     * @param CurlRequest $request
     * @param CurlError $error
     */
    public function __construct(CurlRequest $request, CurlError $error)
    {
        parent::__construct($request,null,$error);
    }
}