<?php
/**
 * Created by PhpStorm.
 * User: stk2k
 * Date: 2016/01/08
 * Time: 3:55
 */

namespace Grasshopper;

use Grasshopper\curl\CurlRequest;
use Grasshopper\curl\CurlRequestHeader;

class JsonPostRequest extends CurlRequest
{
    private $post_data;
    
    /**
     * Constructs HTTP Post request object
     *
     * @param string $url
     * @param array $post_data
     * @param array $options
     */
    public function __construct($url, $post_data, $options = array())
    {
        // set content-type header to 'application/json'
        if ( !isset($options['http_header']) ){
            $http_header = new CurlRequestHeader();
        }
        else if ( $options['http_header'] instanceof CurlRequestHeader){
            $http_header = $options['http_header'];
        }
        else if ( is_array($options['http_header'])){
            $http_header = new CurlRequestHeader($options['http_header']);
        }
        else{
            $http_header = new CurlRequestHeader();
        }
        /** @var CurlRequestHeader $header */
        $http_header->setHeader('Content-Type','application/json');
        
        $options['post_fields'] = json_encode($post_data,JSON_FORCE_OBJECT);
        $options['post'] = true;
        $options['http_header'] = $http_header;
        
        $this->post_data = $post_data;
        parent::__construct( $url, $options );
    }
    
    /**
     * get post data
     *
     * @return array
     */
    public function getPostData()
    {
        return $this->post_data;
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
            CURLOPT_CUSTOMREQUEST => 'POST',
        );
        return array_replace( $options, $override );
    }
}