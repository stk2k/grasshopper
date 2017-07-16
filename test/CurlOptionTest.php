<?php

use \Grasshopper\curl\CurlOption;

class CurlOptionTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testGetString()
    {
        if ( version_compare(PHP_VERSION,'4.0.2') < 0 ) {
            echo 'This library requires PHP version >= 4.0.2';
            return;
        }
        
        $actual = CurlOption::getString(CURLOPT_POSTREDIR);
        $this->assertEquals('CURLOPT_POSTREDIR', $actual);
    
        $actual = CurlOption::getString(CURLOPT_CONNECTTIMEOUT_MS);
        $this->assertEquals('CURLOPT_CONNECTTIMEOUT_MS', $actual);
    
        $actual = CurlOption::getString(CURLOPT_KEYPASSWD);
        $this->assertEquals('CURLOPT_KEYPASSWD', $actual);
    
        $actual = CurlOption::getString(CURLOPT_READFUNCTION);
        $this->assertEquals('CURLOPT_READFUNCTION', $actual);
    
        $actual = CurlOption::getString(CURLOPT_KRB4LEVEL);
        $this->assertEquals('CURLOPT_KRB4LEVEL', $actual);
    
        if ( version_compare(PHP_VERSION,'5.0.0') >= 0 ) {
            $actual = CurlOption::getString(CURLOPT_HTTPAUTH);
            $this->assertEquals('CURLOPT_HTTPAUTH', $actual);
            
            $actual = CurlOption::getString(CURLOPT_PROXYTYPE);
            $this->assertEquals('CURLOPT_PROXYTYPE', $actual);
        }
    
        if ( version_compare(PHP_VERSION,'5.1.0') >= 0 ) {
            $actual = CurlOption::getString(CURLOPT_AUTOREFERER);
            $this->assertEquals('CURLOPT_AUTOREFERER', $actual);
        
            $actual = CurlOption::getString(CURLOPT_TIMECONDITION);
            $this->assertEquals('CURLOPT_TIMECONDITION', $actual);
        }
    
        if ( version_compare(PHP_VERSION,'5.2.10') >= 0 ) {
            $actual = CurlOption::getString(CURLOPT_PROTOCOLS);
            $this->assertEquals('CURLOPT_PROTOCOLS', $actual);
        }
    
        if ( version_compare(PHP_VERSION,'7.0.7') >= 0 ) {
            $actual = CurlOption::getString(CURLOPT_PROXYHEADER);
            $this->assertEquals('CURLOPT_PROXYHEADER', $actual);
            
            $actual = CurlOption::getString(CURLOPT_PIPEWAIT);
            $this->assertEquals('CURLOPT_PIPEWAIT', $actual);
    
            $actual = CurlOption::getString(CURLOPT_SSL_FALSESTART);
            $this->assertEquals('CURLOPT_SSL_FALSESTART', $actual);
            
            $actual = CurlOption::getString(CURL_HTTP_VERSION_2TLS);
            $this->assertEquals('CURL_HTTP_VERSION_2TLS', $actual);
        }
    }

}