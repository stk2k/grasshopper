<?php
namespace Grasshopper;

class HttpGetRequest extends CurlRequest
{
    /**
     * Constructs HTTP Get request object
     *
     * @param string $url
     * @param array $options
     */
    public function __construct($url, array $options = [])
    {
        parent::__construct( 'GET', $url, $options );
    }
}