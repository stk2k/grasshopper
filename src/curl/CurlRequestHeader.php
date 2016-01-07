<?php
namespace Grasshopper\curl;

use Grasshopper\Grasshopper;
use Grasshopper\util\Sanitizer;

class CurlRequestHeader
{
    /** @var  array */
    private $headers;

    /**
     * Constructs grasshopper object
     *
     * @param array $headers
     */
    public function __construct(array $headers = [])
    {
        // options
        $dafaults = [
            'Content-type' => 'text/plain',
            'User-Agent' => Grasshopper::DEFAULT_USERAGENT,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-us;q=0.7,en;q=0.3',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Connection' => 'keep-alive',
            'Keep-Alive' => '300',
            'Cache-Control' => 'max-age=0',
            'Pragma' => '',
        ];
        $user_headers = [
            'Content-type' => isset($headers['content_type']) ? $headers['content_type'] : null,
            'User-Agent' => isset($headers['user_agent']) ? $headers['user_agent'] : null,
            'Accept' => isset($headers['accept']) ? $headers['accept'] : null,
            'Accept-Language' => isset($headers['accept_language']) ? $headers['accept_language'] : null,
            'Accept-Encoding' => isset($headers['accept_encoding']) ? $headers['accept_encoding'] : null,
            'Accept-Charset' => isset($headers['accept_charset']) ? $headers['accept_charset'] : null,
            'Connection' => isset($headers['connection']) ? $headers['connection'] : null,
            'Keep-Alive' => isset($headers['keep_alive']) ? $headers['keep_alive'] : null,
            'Cache-Control' => isset($headers['cache_control']) ? $headers['cache_control'] : null,
            'Pragma' => isset($headers['pragma']) ? $headers['pragma'] : null,
        ];

        // merge headers between user and defaults
        $real_headers = [];
        foreach($dafaults as $k => $v){
            $user_val = isset($user_headers[$k]) ? $user_headers[$k] : null;
            $real_headers[$k] = $user_val ? $user_val : $v;
        }
        $this->headers = $real_headers;
    }

    /**
     * compile headers into array for cURL option
     *
     * @return array
     */
    public function compile()
    {
        $compiled = [];
        foreach( $this->headers as $k => $v ){
            $compiled[$k] = Sanitizer::removeControlChars($v);
        }
        return $compiled;
    }
}