<?php
namespace Grasshopper;

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
}