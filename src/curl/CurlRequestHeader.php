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
    public function __construct(array $headers = array())
    {
        // options
        $dafaults = [
            'Content-Type' => 'text/plain',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-us;q=0.7,en;q=0.3',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Connection' => 'keep-alive',
            'Keep-Alive' => '300',
            'Cache-Control' => 'max-age=0',
            'Pragma' => '',
        ];

        // merge headers between user and defaults
        $real_headers = array_merge($dafaults, $headers);

        if ( isset($real_headers['Keep-Alive']) && isset($real_headers['Connection']) && $real_headers['Connection'] != 'keep-alive' ){
            unset($real_headers['Keep-Alive']);
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
        $compiled = array();
        foreach( $this->headers as $k => $v ){
            $v = Sanitizer::removeControlChars($v);
            if (is_string($k)){
                $compiled[] = "$k: $v";
            }
            elseif( is_integer($k)){
                $compiled[] = $v;
            }
        }
        return $compiled;
    }
    
    /**
     * add headers
     *
     * @param string $key
     * @param string $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }
    
    /**
     * return headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}