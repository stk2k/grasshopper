<?php
namespace Grasshopper;

use \Grasshopper\event\SuccessEvent;
use \Grasshopper\curl\CurlHandlePool;

class GrasshopperTest extends \PhpUnit_Framework_TestCase
{
    const LOCAL_PORT = '8082';

    private $url_base;

    protected function setUp()
    {
        $this->url_base = 'http://localhost:' . self::LOCAL_PORT;
    }

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
                new HttpGetRequest($this->url_base . '/test1.html'),
                new HttpGetRequest($this->url_base . '/test1.html'),
            ]
        );

        $this->assertEquals(2, count($hopper->getRequests()) );
    }

    public function testReset()
    {
        $hopper = new Grasshopper();

        $hopper->addRequests(
            [
                new HttpGetRequest($this->url_base . '/test1.html'),
                new HttpGetRequest($this->url_base . '/test1.html'),
            ]
        );

        $hopper->reset();

        $this->assertEquals(0, count($hopper->getRequests()) );
    }

    public function test200(){
        $hopper = new Grasshopper();

        $url = $this->url_base . '/test1.html';

        $hopper->addRequest(new HttpGetRequest($url));

        $result = $hopper->waitForAll();

        $this->assertEquals(1, count($result) );
        $this->assertEquals(true, isset($result[$url]) );

        /** @var SuccessEvent $res */
        $res = $result[$url];

        $this->assertEquals(true, $res instanceof SuccessEvent );

        $status = $res->getResponse()->getStatusCode();

        $this->assertEquals(200, $status );
    }

    public function test404(){
        $hopper = new Grasshopper();

        $url = $this->url_base . '/404.html';

        $hopper->addRequest(new HttpGetRequest($url));

        $result = $hopper->waitForAll();

        $this->assertEquals(1, count($result) );
        $this->assertEquals(true, isset($result[$url]) );

        /** @var SuccessEvent $res */
        $res = $result[$url];

        $this->assertEquals(true, $res instanceof SuccessEvent );

        $status = $res->getResponse()->getStatusCode();

        $this->assertEquals(404, $status );
    }

    public function testWaitForAll()
    {
        ini_set( 'memory_limit', -1 );

        $url = $this->url_base . '/test1.html';

        $hopper = new Grasshopper();
        $refHopper = new \ReflectionClass($hopper);

        $refPool = $refHopper->getProperty('pool');
        $refPool->setAccessible(true);

        /** @var CurlHandlePool $pool */
        $pool = $refPool->getValue($hopper);
        $pool_items = $pool->availableCount();
        $this->assertEquals(Grasshopper::DEFAULT_POOL_SIZE, $pool_items );

        $options = [
            'max_download_size' => 10485760,   // 10MB
            ];

        $hopper->addRequest(new HttpGetRequest($url, $options));

        $result = $hopper->waitForAll();

        $pool_items = $pool->availableCount();
        $this->assertEquals(Grasshopper::DEFAULT_POOL_SIZE, $pool_items );

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