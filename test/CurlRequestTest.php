<?php
namespace Grasshopper;

use \Grasshopper\curl\CurlRequest;

class CurlRequestTest extends \PhpUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testIsVerboseDefault()
    {
        $options = array();

        $req = new CurlRequest('GET','http://sample.com', $options);

        $actual = $req->isVerbose();

        $this->assertEquals(false, $actual);
    }


    public function testIsVerboseSet()
    {
        $options = array(
            'verbose' => true
        );

        $req = new CurlRequest('GET','http://sample.com', $options);

        $actual = $req->isVerbose();

        $this->assertEquals(true, $actual);
    }
}