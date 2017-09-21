<?php
use Grasshopper\HttpGetRequest;
use Grasshopper\Grasshopper;
use Grasshopper\event\SuccessEvent;

class HttpGetRequestTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testNoQuery()
    {
        $req = new HttpGetRequest('http://example.com');

        $this->assertEquals('http://example.com', $req->getUrl());
        $this->assertEmpty($req->getQueryData());
    }
    
    public function testSimpleQuery()
    {
        $query = array('foo'=>'bar');
        $req = new HttpGetRequest('http://example.com', $query);
        
        $this->assertEquals('http://example.com?foo=bar', $req->getUrl());
        $this->assertEquals($query, $req->getQueryData());
    }
    
    public function testSomeQuery()
    {
        $query = array('foo'=>'bar','fruits'=>'apple');
        $req = new HttpGetRequest('http://example.com', $query);
        
        $this->assertEquals('http://example.com?foo=bar&fruits=apple', $req->getUrl());
        $this->assertEquals($query, $req->getQueryData());
    }
    
    /**
     * Need to run /bin/http_server.php before running this test
     */
    public function testRequestGet()
    {
        $url = 'http://localhost:8080';
        $req = new HttpGetRequest($url);
    
        $hopper = new Grasshopper();
        
        $hopper->addRequest($req);
        
        $result = $hopper->waitForAll();
        
        $res = $result[$url];
        
        if ($res instanceof SuccessEvent){
            $headers = $res->getResponse()->getHeadersParsed();
            echo print_r($headers,true) . PHP_EOL;
            
            $this->assertEquals(200, $res->getResponse()->getStatusCode());
            $this->assertEquals('GET', $headers['Method']);
        }
        else{
            $this->fail('GET request returned failure result');
        }
    }
}