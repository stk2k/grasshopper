<?php
use Grasshopper\HttpPostRequest;

class HttpPostRequestTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testNoData()
    {
        $req = new HttpPostRequest('http://example.com',array());

        $this->assertEquals('http://example.com', $req->getUrl());
        $this->assertEmpty($req->getPostData());
    }
    
    public function testSimpleQuery()
    {
        $post_data = array('foo'=>'bar');
        $req = new HttpPostRequest('http://example.com', $post_data);
        
        $this->assertEquals('http://example.com', $req->getUrl());
        $this->assertEquals(1, count($req->getPostData()));
        $this->assertEquals($post_data, $req->getPostData());
    }
    
    public function testSomeQuery()
    {
        $post_data = array('foo'=>'bar','fruits'=>'apple');
        $req = new HttpPostRequest('http://example.com', $post_data);
    
        $this->assertEquals('http://example.com', $req->getUrl());
        $this->assertEquals(2, count($req->getPostData()));
        $this->assertEquals($post_data, $req->getPostData());
    }
    
    public function testContentType()
    {
        $req = new HttpPostRequest('http://example.com',array());
        
        $options = $req->getOptions();
        
        $this->assertTrue(isset($options[CURLOPT_HTTPHEADER]));
        $this->assertContains('Content-Type: application/x-www-form-urlencoded',$options[CURLOPT_HTTPHEADER]);
    }
    
}