<?php
use Grasshopper\HttpHeadRequest;
use Grasshopper\Grasshopper;
use Grasshopper\event\SuccessEvent;

class HttpHeadRequestTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testNoQuery()
    {
        $req = new HttpHeadRequest('http://example.com');

        $this->assertEquals('http://example.com', $req->getUrl());
        $this->assertEmpty($req->getQueryData());
    }
    
    public function testSimpleQuery()
    {
        $query = array('foo'=>'bar');
        $req = new HttpHeadRequest('http://example.com', $query);
        
        $this->assertEquals('http://example.com?foo=bar', $req->getUrl());
        $this->assertEquals($query, $req->getQueryData());
    }
    
    public function testSomeQuery()
    {
        $query = array('foo'=>'bar','fruits'=>'apple');
        $req = new HttpHeadRequest('http://example.com', $query);
        
        $this->assertEquals('http://example.com?foo=bar&fruits=apple', $req->getUrl());
        $this->assertEquals($query, $req->getQueryData());
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
            $this->assertEquals('HEAD', $headers['Method']);
        }
        else{
            $this->fail('GET request returned failure result');
        }
    }
}