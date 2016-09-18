<?php
namespace Grasshopper;

use Grasshopper\curl\CurlRequest;

class CurlRequestTest extends \PhpUnit_Framework_TestCase
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

        $req = new HttpGetRequest('http://sample.com', $options);

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
    }

    public function testGetTimeout()
    {
        $options = array(
            'timeout' => 999
        );

        $req = new HttpGetRequest('http://sample.com',$options);

        $actual = $req->getTimeout();

        $this->assertEquals(999, $actual);
    }

    public function testGetTimeoutMS()
    {
        $options = array(
            'timeout_ms' => 999
        );

        $req = new HttpGetRequest('http://sample.com',$options);

        $actual = $req->getTimeout(true);

        $this->assertEquals(999, $actual);
    }

    public function testGetConnectTimeoutDefault()
    {
        $req = new HttpGetRequest('http://sample.com',array());

        $actual = $req->getConnectTimeout();

        $this->assertEquals(CurlRequest::DEFAULT_CONNECT_TIMEOUT, $actual);
    }

    public function testGetConnectTimeout()
    {
        $options = array(
            'connect_timeout' => 999
        );

        $req = new HttpGetRequest('http://sample.com',$options);

        $actual = $req->getConnectTimeout();

        $this->assertEquals(999, $actual);
    }

    public function testGetConnectTimeoutMS()
    {
        $options = array(
            'connect_timeout_ms' => 999
        );

        $req = new HttpGetRequest('http://sample.com',$options);

        $actual = $req->getConnectTimeout(true);

        $this->assertEquals(999, $actual);
    }


}