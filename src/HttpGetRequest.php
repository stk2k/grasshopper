<?php
namespace Grasshopper;

use Grasshopper\curl\CurlRequest;

class HttpGetRequest extends CurlRequest
{
    /**
     * Constructs HTTP Get request object
     *
     * @param string $url
     * @param array $options
     */
    public function __construct($url, $options = null)
    {
        parent::__construct( $url, $options );
    }
    
    /**
     * Override curl options
     *
     * @param array $options
     *
     * @return array
     */
    protected function overrideOptions($options)
    {
        $override = array(
            CURLOPT_CUSTOMREQUEST => 'GET',
        );
        return array_replace( $options, $override );
    }
}