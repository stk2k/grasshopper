<?php
namespace Grasshopper;

use Grasshopper\curl\CurlRequest;

class HttpHeadRequest extends CurlRequest
{
    /**
     * Constructs HTTP HEAD request object
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
            CURLOPT_CUSTOMREQUEST => 'HEAD',
            CURLOPT_NOBODY => true
        );
        return array_replace( $options, $override );
    }
    
}