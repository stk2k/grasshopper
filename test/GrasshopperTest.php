<?php
namespace Grasshopper;

use \Grasshopper\CurlRequest;
use \Grasshopper\CurlResponse;

class GrasshopperTest extends \PhpUnit_Framework_TestCase
{
    public function testWaitForAll()
    {
        $url = 'http://localhost:8000/test1.html';

        $hopper = new Grasshopper();

        $hopper->addRequest(new CurlRequest($url));

        $result = $hopper->waitForAll();

        //var_dump($result);

        /** @var CurlResponse $res */
        $res = $result[$url];

        $this->assertEquals('Grasshopper\CurlResponse', get_class($res) );

        //echo 'body:' . $res->getBody() . PHP_EOL;

        $doc = new \DOMDocument();
        @$doc->loadHTML($res->getBody());

        $xpath = new \DOMXpath( $doc );

        $title = $xpath->query('//head/title[1]')->item(0)->nodeValue;

        $this->assertEquals('test data1', $title );

        $boo = $xpath->query('//body')->item(0)->nodeValue;

        $this->assertEquals('boo', trim($boo) );

        echo "finished." . PHP_EOL;

    }
}