<?php
namespace Grasshopper;

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
        ini_set( 'memory_limit', -1 );

        $url = 'http://localhost:8082/test1.html';

        $hopper = new Grasshopper();

        $options = [
            'max_download_size' => 10485760,   // 10MB
            ];

        $hopper->addRequest(new HttpGetRequest($url, $options));

        $result = $hopper->waitForAll();

        //var_dump($result);

        /** @var SuccessEvent $res */
        $res = $result[$url];
        //var_dump($res);

        $this->assertEquals('Grasshopper\event\SuccessEvent', get_class($res) );

        $body = $res->getResponse()->getBody();

        //echo "body: $body" . PHP_EOL;

        $doc = new \DOMDocument();
        @$doc->loadHTML($body);

        $xpath = new \DOMXpath( $doc );

        $elements = $xpath->query('//head/title[1]');
        $title = $elements->item(0)->nodeValue;

        $this->assertEquals('test data1', $title );

        $boo = $xpath->query('//body')->item(0)->nodeValue;

        $this->assertEquals('boo', trim($boo) );

        echo "finished." . PHP_EOL;

    }
}