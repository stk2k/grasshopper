<?php
namespace Grasshopper;

use Grasshopper\curl\CurlRequest;

class HttpGetRequest extends CurlRequest
{
    private $query_data;
    
    /**
     * Constructs HTTP Get request object
     *
     * @param string $url
     * @param array $query_data
     * @param array $options
     */
    public function __construct($url, $query_data = null, $options = null)
    {
        if ($query_data && !empty($query_data)){
            $url .= '?' . http_build_query($query_data);
        }
        $this->query_data = $query_data;
        parent::__construct( 'GET', $url, $options );
    }
    
    /**
     * get query data
     *
     * @return array
     */
    public function getQueryData()
    {
        return $this->query_data;
    }
}