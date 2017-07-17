<?php
use Grasshopper\JsonPostRequest;

class JsonPostRequestTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testNoData()
    {
        $req = new JsonPostRequest('http://example.com',array());

        $this->assertEquals('http://example.com', $req->getUrl());
        $this->assertEmpty($req->getPostData());
    }
    
    public function testSimpleQuery()
    {
        $post_data = array('foo'=>'bar');
        $req = new JsonPostRequest('http://example.com', $post_data);
        
        $this->assertEquals('http://example.com', $req->getUrl());
        $this->assertEquals(1, count($req->getPostData()));
        $this->assertEquals($post_data, $req->getPostData());
    }
    
    public function testSomeQuery()
    {
        $post_data = array('foo'=>'bar','fruits'=>'apple');
        $req = new JsonPostRequest('http://example.com', $post_data);
    
        $this->assertEquals('http://example.com', $req->getUrl());
        $this->assertEquals(2, count($req->getPostData()));
        $this->assertEquals($post_data, $req->getPostData());
    }
    
    public function testContentType()
    {
        $req = new JsonPostRequest('http://example.com',array());
        
        $options = $req->getOptions();
        
        $this->assertTrue(isset($options[CURLOPT_HTTPHEADER]));
        $this->assertContains('Content-Type: application/json',$options[CURLOPT_HTTPHEADER]);
    }
    
    public function testPostFields()
    {
        $post_data = array('foo'=>'bar','fruits'=>'apple');
        $req = new JsonPostRequest('http://example.com',$post_data);
        
        $options = $req->getOptions();
        
        $this->assertTrue(isset($options[CURLOPT_POSTFIELDS]));
        $this->assertInternalType('string',$options[CURLOPT_POSTFIELDS]);
        $this->assertInternalType('object',json_decode($options[CURLOPT_POSTFIELDS], false));
    }
    
}