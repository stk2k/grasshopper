<?php
namespace Grasshopper\curl;

use Grasshopper\exception\DeflateException;

class CurlResponse
{
    /** @var string */
    private $protocol;

    /** @var int */
    private $http_code;

    /** @var string */
    private $reason_phrase;

    /** @var string */
    private $protocol_version;

    /** @var string */
    private $url;

    /** @var string */
    private $content_type;

    /** @var string */
    private $host;

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

    /** @var int */
    private $download_content_length;

    /**
     * Constructs grasshopper object
     *
     * @param array $info
     * @param string $content
     * @throws DeflateException
     */
    public function __construct(array $info, $content)
    {
        $this->http_code = $info['http_code'];
        $this->content_type = $info['content_type'];
        $this->url = $info['url'];
        $this->effective_url = $info['effective_url'];
        $this->download_content_length = $info['download_content_length'];

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

        // convert body and reason phrase encoding to utf8
        $this->body = $this->charset ? $this->convertEncoding($body, $this->charset) : $body;
        $this->reason_phrase = $this->charset ? $this->convertEncoding($this->reason_phrase, $this->charset) : $this->reason_phrase;
    }

    /**
     * Get status code
     *
     * @return int
     */
    public function getStatusCode(){
        return $this->http_code;
    }

    /**
     * Get reason phrase
     *
     * @return string
     */
    public function getReasonPhrase(){
        return $this->reason_phrase;
    }

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol(){
        return $this->protocol;
    }

    /**
     * Get protocol version
     *
     * @return string
     */
    public function getProtocolVersion(){
        return $this->protocol_version;
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
     * Get content length
     *
     * @return string
     */
    public function getContentLength(){
        return $this->download_content_length;
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

        $status_line = isset($this->headers[0]) ? trim($this->headers[0]) : null;

        if ( $status_line ){
            $parts = explode(' ', $status_line, 3);
            $protocol_and_version = isset($parts[0]) ? $parts[0] : '';
            $this->reason_phrase = isset($parts[2]) ? $parts[2] : '';

            $p = strpos($protocol_and_version, '/');
            $this->protocol = ($p !== false) ? substr($protocol_and_version,0,$p) : $protocol_and_version;
            $this->protocol_version = ($p !== false) ? substr($protocol_and_version,$p+1) : '';
        }


        foreach( $this->headers as $h ){
            if ( preg_match( '@Content-Encoding:\s+([\w/+]+)@i', $h, $matches ) ){
                $this->content_encoding = isset($matches[1]) ? strtolower($matches[1]) : null;
            }
            if ( preg_match( '@Content-Type:\s*([\w/+-\/]+);\s*charset=\s*([\w/+\-]+)@i', $h, $matches ) ){
                $this->charset = isset($matches[2]) ? strtolower($matches[2]) : null;
            }
            elseif ( preg_match( '@Host:\s+([\w/:+]+)@i', $h, $matches ) ){
                $this->host = isset($matches[1]) ? strtolower($matches[1]) : null;
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
        $charset = isset($matches[3]) ? $matches[3] : $this->charset;

        $php_encoding = $charset ? self::getPhpEncoding($charset) : null;
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
                $body = @zlib_decode($body);
                if ( $body === FALSE ){
                    throw new DeflateException();
                }
                return $body;
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
     * Convert encoding
     * @param string $str
     * @param string $html_charset
     * @param string $to_encoding
     * @return array
     */
    private static function convertEncoding( $str, $html_charset, $to_encoding = 'UTF-8' )
    {
        $php_encoding = self::getPhpEncoding($html_charset);
        $from_encoding = $php_encoding ? $php_encoding : 'auto';

        $str = ( $from_encoding == $to_encoding ) ? $str : mb_convert_encoding( $str, $to_encoding, $from_encoding );

        return $str;
    }
}