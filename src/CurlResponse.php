<?php
namespace Grasshopper;


class CurlResponse
{
    /** @var string */
    private $request_url;

    /** @var string */
    private $url;

    /** @var int */
    private $http_code;

    /** @var string */
    private $content_type;

    /** @var string */
    private $effective_url;

    /** @var string */
    private $body;

    /** @var array */
    private $headers;

    /** @var string */
    private $charset;

    /** @var string */
    private $content_encoding;


    /**
     * Constructs grasshopper object
     *
     * @param string $request_url
     * @param array $info
     * @param string $content
     */
    public function __construct($request_url, array $info, $content)
    {
        $this->request_url = $request_url;
        $this->http_code = $info['http_code'];
        $this->content_type = $info['content_type'];
        $this->url = $info['url'];
        $this->effective_url = $info['effective_url'];

        // get header from content
        $header = substr($content, 0, $info["header_size"]);
        $header = strtr($header, ["\r\n"=>"\n","\r"=>"\n"]);

        // parser header
        $this->parseHeader($header);

        // get body from content
        $body = substr($content, $info["header_size"]);

        // deflate compressed data
        $body = $this->content_encoding ? $this->deflateCompressedData($body) : $body;

        // detect character encoding
        $this->charset = $this->detectCharset($body);

        // convert body encoding to utf8
        $this->body = $this->charset ? $this->convertBody($body, $this->charset) : $body;
    }

    /**
     * Get request URL
     *
     * @return string
     */
    public function getRequestUrl(){
        return $this->request_url;
    }

    /**
     * Get HTTP status code
     *
     * @return int
     */
    public function getStatusCode(){
        return $this->http_code;
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType(){
        return $this->content_type;
    }

    /**
     * Get charset
     *
     * @return string
     */
    public function getCharset(){
        return $this->charset;
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * Get effective URL
     *
     * @return string
     */
    public function getEffectiveUrl(){
        return $this->effective_url;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody(){
        return $this->body;
    }

    /**
     * Get headers
     *
     * @return array
     */
    public function getHeaders(){
        return $this->headers;
    }

    /**
     * parse headers
     *
     * @param string $header
     */
    private function parseHeader($header)
    {
        $this->headers = array_filter(explode("\n",$header));

        foreach( $this->headers as $h ){
            if ( preg_match( '@Content-Encoding:\s+([\w/+]+)@i', $h, $matches ) ){
                $this->content_encoding = isset($matches[1]) ? strtolower($matches[1]) : null;
            }

        }
    }

    /**
     * detect charset
     *
     * @param string $body
     *
     * @return string
     */
    private function detectCharset( $body )
    {
        // get character encoding from Content-Type header
        preg_match( '@([\w/+]+)(;\s+charset=(\S+))?@i', $this->content_type, $matches );
        $charset = isset($matches[3]) ? $matches[3] : null;

        $php_encoding = self::getPhpEncoding($this->charset);
        if ( !$php_encoding ){
            $html_encoded = mb_convert_encoding( strtolower($body), 'HTML-ENTITIES', 'UTF-8' );
            $doc = new \DOMDocument();
            @$doc->loadHTML( $html_encoded );
            $elements = $doc->getElementsByTagName( "meta" );

            for($i = 0; $i < $elements->length; $i++) {
                $e = $elements->item($i);

                // find like: <meta charset="utf-8"/>
                $node = $e->attributes->getNamedItem("charset");
                if($node){
                    $charset = $node->nodeValue;
                    $php_encoding = self::getPhpEncoding($charset);
                    break;
                }

                // find like: <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                $node = $e->attributes->getNamedItem("http-equiv");
                if ( $node ){
                    if ( strcasecmp($node->nodeValue, 'content-type') == 0 ){
                        $node = $e->attributes->getNamedItem("content");
                        if( $node ){
                            if ( preg_match('/[\; ]charset ?\= ?([A-Za-z0-9\-\_]+)/', $node->nodeValue, $m) ){
                                $charset = $m[1];
                                $php_encoding = self::getPhpEncoding($charset);
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $php_encoding;
    }

    /**
     * deflate compressed data
     *
     * @param string $body
     *
     * @return string
     */
    private function deflateCompressedData($body)
    {
        switch($this->content_encoding){
            case 'gzip':
            case 'deflate':
            case 'compress':
                return zlib_decode($body);
                break;
            default:
                return $body;
                break;
        }
    }

    /**
     * Get PHP character encoding
     *
     * @param $html_charset
     *
     * @return string
     */
    private static function getPhpEncoding( $html_charset )
    {
        $php_encoding = null;

        switch( strtolower($html_charset) ){
            case 'sjis':
            case 'sjis-win':
            case 'shift_jis':
            case 'shift-jis':
            case 'ms_kanji':
            case 'csshiftjis':
            case 'x-sjis':
            $php_encoding = 'sjis-win';
                break;
            case 'euc-jp':
            case 'cseucpkdfmtjapanese':
            $php_encoding = 'EUC-JP';
                break;
            case 'jis':
                $php_encoding = 'jis';
                break;
            case 'iso-2022-jp':
            case 'csiso2022jp':
            $php_encoding = 'ISO-2022-JP';
                break;
            case 'iso-2022-jp-2':
            case 'csiso2022jp2':
            $php_encoding = 'ISO-2022-JP-2';
                break;
            case 'utf-8':
            case 'csutf8':
            $php_encoding = 'UTF-8';
                break;
            case 'utf-16':
            case 'csutf16':
            $php_encoding = 'UTF-16';
                break;
            default:
                if ( strpos($html_charset,'sjis') ){
                    $php_encoding = 'SJIS';
                }
                break;
        }

        return $php_encoding;
    }

    /**
     * Convert body encoding
     * @param $body
     * @param $html_charset
     * @param string $to_encoding
     * @return array
     */
    private static function convertBody( $body, $html_charset, $to_encoding = 'UTF-8' )
    {
        $php_encoding = self::getPhpEncoding($html_charset);
        $from_encoding = $php_encoding ? $php_encoding : 'auto';

        $body = ( $from_encoding == $to_encoding ) ? $body : mb_convert_encoding( $body, $to_encoding, $from_encoding );

        return $body;
    }
}