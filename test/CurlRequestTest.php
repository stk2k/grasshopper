<?php
use Grasshopper\HttpGetRequest;
use Grasshopper\HttpPostRequest;
use Grasshopper\curl\CurlRequest;

class CurlRequestTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testIsVerboseDefault()
    {
        $req = new HttpGetRequest('http://sample.com');

        $actual = $req->isVerbose();

        $this->assertEquals(false, $actual);
    }

    public function testIsVerboseSet()
    {
        $options = array(
            'verbose' => true
        );

        $req = new HttpGetRequest('http://sample.com', null, $options);

        $actual = $req->isVerbose();

        $this->assertEquals(true, $actual);
    }

    public function testGetMethodDefault()
    {
        $req = new HttpGetRequest('http://sample.com');

        $actual = $req->getMethod();

        $this->assertEquals('GET', $actual);
    }

    public function testGetMethodPost()
    {
        $req = new HttpPostRequest('http://sample.com',array());

        $actual = $req->getMethod();

        $this->assertEquals('POST', $actual);
    }

    public function testGetTimeoutDefault()
    {
        $req = new HttpGetRequest('http://sample.com',array());

        $actual = $req->getTimeout();

        $this->assertEquals(CurlRequest::DEFAULT_TIMEOUT, $actual);

        $options = $req->getOptions();

        $this->assertEquals(true, isset($options[CURLOPT_TIMEOUT]));
        $this->assertEquals(false, isset($options[CURLOPT_TIMEOUT_MS]));
    }

    public function testGetTimeout()
    {
        $options = array(
            'timeout' => 999
        );

        $req = new HttpGetRequest('http://sample.com', null, $options);

        $actual = $req->getTimeout();

        $this->assertEquals(999, $actual);

        $options = $req->getOptions();

        $this->assertEquals(true, isset($options[CURLOPT_TIMEOUT]));
        $this->assertEquals(false, isset($options[CURLOPT_TIMEOUT_MS]));
    }

    public function testGetTimeoutMS()
    {
        $options = array(
            'timeout_ms' => 999
        );

        $req = new HttpGetRequest('http://sample.com',null, $options);

        $actual = $req->getTimeout(true);

        $this->assertEquals(999, $actual);

        $options = $req->getOptions();

        $this->assertEquals(false, isset($options[CURLOPT_TIMEOUT]));
        $this->assertEquals(true, isset($options[CURLOPT_TIMEOUT_MS]));
    }

    public function testGetConnectTimeoutDefault()
    {
        $req = new HttpGetRequest('http://sample.com',array());

        $actual = $req->getConnectTimeout();

        $this->assertEquals(CurlRequest::DEFAULT_CONNECTTIMEOUT, $actual);

        $options = $req->getOptions();

        $this->assertEquals(true, isset($options[CURLOPT_CONNECTTIMEOUT]));
        $this->assertEquals(false, isset($options[CURLOPT_CONNECTTIMEOUT_MS]));
    }

    public function testGetConnectTimeout()
    {
        $options = array(
            'connect_timeout' => 999
        );

        $req = new HttpGetRequest('http://sample.com', null, $options);

        $actual = $req->getConnectTimeout();

        $this->assertEquals(999, $actual);

        $options = $req->getOptions();

        $this->assertEquals(true, isset($options[CURLOPT_CONNECTTIMEOUT]));
        $this->assertEquals(false, isset($options[CURLOPT_CONNECTTIMEOUT_MS]));
    }

    public function testGetConnectTimeoutMS()
    {
        $options = array(
            'connect_timeout_ms' => 999
        );

        $req = new HttpGetRequest('http://sample.com', null, $options);

        $actual = $req->getConnectTimeout(true);

        $this->assertEquals(999, $actual);

        $options = $req->getOptions();

        $this->assertEquals(false, isset($options[CURLOPT_CONNECTTIMEOUT]));
        $this->assertEquals(true, isset($options[CURLOPT_CONNECTTIMEOUT_MS]));
    }


}