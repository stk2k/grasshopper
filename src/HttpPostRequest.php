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
use Grasshopper\util\Sanitizer;


class HttpPostRequest extends CurlRequest
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
        // set content-type header to 'application/x-www-form-urlencoded'
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
        $http_header->setHeader('Content-Type','application/x-www-form-urlencoded');
        
        $options['post_fields'] = http_build_query(Sanitizer::removeControlChars($post_data));
        $options['post'] = true;
        $options['http_header'] = $http_header;
        
        $this->post_data = $post_data;
        parent::__construct( 'POST', $url, $options );
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
}