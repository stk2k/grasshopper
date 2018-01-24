<?php
use Grasshopper\HttpHeadRequest;
use Grasshopper\Grasshopper;
use Grasshopper\event\SuccessEvent;

class HttpHeadRequestTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    /**
     * Need to run /bin/http_server.php before running this test
     */
    public function testRequestGet()
    {
        $url = 'http://localhost:8080';
        $req = new HttpHeadRequest($url);
        
        $hopper = new Grasshopper();
        
        $hopper->addRequest($req);
        
        $result = $hopper->waitForAll();
        
        $res = $result[$url];
        
        if ($res instanceof SuccessEvent){
            $headers = $res->getResponse()->getHeadersParsed();
            echo print_r($headers,true) . PHP_EOL;
            
            $this->assertEquals(200, $res->getResponse()->getStatusCode());
        }
        else{
            $this->fail('GET request returned failure result');
        }
    }
}