<?php
namespace Grasshopper;

use \Grasshopper\HttpGetRequest;
use \Grasshopper\HttpPostRequest;
use \Grasshopper\event\SuccessEvent;

class GrasshopperTest extends \PhpUnit_Framework_TestCase
{
    public function testAddRequest()
    {
        $hopper = new Grasshopper();

        $this->assertEquals(0, count($hopper->getRequests()) );

        $hopper->addRequest(new HttpGetRequest('http://localhost:8000/test1.html'));

        $this->assertEquals(1, count($hopper->getRequests()) );
    }

    public function testAddRequests()
    {
        $hopper = new Grasshopper();

        $this->assertEquals(0, count($hopper->getRequests()) );

        $hopper->addRequests(
            [
                new HttpGetRequest('http://localhost:8000/test1.html'),
                new HttpGetRequest('http://localhost:8000/test1.html'),
            ]
        );

        $this->assertEquals(2, count($hopper->getRequests()) );
    }

    public function testWaitForAll()
    {
        $url = 'http://localhost:8000/test1.html';
        $url = 'http://thisisassss.com/test1.html';

        $hopper = new Grasshopper();

        $hopper->addRequest(new HttpGetRequest($url));

        $result = $hopper->waitForAll(10,1);

        //var_dump($result);

        /** @var SuccessEvent $res */
        $res = $result[$url];
        var_dump($res);

        $this->assertEquals('Grasshopper\event\SuccessEvent', get_class($res) );

        //echo 'body:' . $res->getResponse()->getBody() . PHP_EOL;

        $doc = new \DOMDocument();
        @$doc->loadHTML($res->getResponse()->getBody());

        $xpath = new \DOMXpath( $doc );

        $title = $xpath->query('//head/title[1]')->item(0)->nodeValue;

        $this->assertEquals('test data1', $title );

        $boo = $xpath->query('//body')->item(0)->nodeValue;

        $this->assertEquals('boo', trim($boo) );

        echo "finished." . PHP_EOL;

    }
}