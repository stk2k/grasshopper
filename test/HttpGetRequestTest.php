<?php
use Grasshopper\HttpGetRequest;

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
}