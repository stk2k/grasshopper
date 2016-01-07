<?php
/**
 * Created by PhpStorm.
 * User: stk2k
 * Date: 2016/01/08
 * Time: 3:55
 */

namespace Grasshopper;

use Grasshopper\curl\CurlRequest;
use Grasshopper\util\Sanitizer;


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
        $options['post_fields'] = http_build_query(Sanitizer::removeControlChars($post_data));
        $options['post'] = true;

        parent::__construct( 'POST', $url, $options );
    }
}