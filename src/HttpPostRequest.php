<?php
/**
 * Created by PhpStorm.
 * User: stk2k
 * Date: 2016/01/08
 * Time: 3:55
 */

namespace Grasshopper;


class HttpPostRequest extends CurlRequest
{
    /**
     * Constructs HTTP Post request object
     *
     * @param string $url
     * @param array $post_data
     * @param array $options
     */
    public function __construct($url, array $post_data, array $options = [])
    {
        $options['post_fields'] = $post_data;
        $options['post'] = true;

        parent::__construct( 'POST', $url, $options );
    }
}