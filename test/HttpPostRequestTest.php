<?php
use Grasshopper\HttpPostRequest;
use Grasshopper\Grasshopper;
use Grasshopper\event\SuccessEvent;

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
    
    public function testPostFields()
    {
        $post_data = array('foo'=>'bar','fruits'=>'apple');
        $req = new HttpPostRequest('http://example.com',$post_data);
        
        $options = $req->getOptions();
        
        $this->assertTrue(isset($options[CURLOPT_POSTFIELDS]));
        $this->assertInternalType('string',$options[CURLOPT_POSTFIELDS]);
    
        $data = null;
        parse_str($options[CURLOPT_POSTFIELDS],$data);
        
        $this->assertTrue(is_array($data));
        $this->assertEquals(2, count($data));
        $this->assertTrue(isset($data['foo']));
        $this->assertTrue(isset($data['fruits']));
        $this->assertEquals('bar', $data['foo']);
        $this->assertEquals('apple', $data['fruits']);
    }
    
    /**
     * Need to run /bin/http_server.php before running this test
     */
    public function testRequestGet()
    {
        $url = 'http://localhost:8080';
        $post_data = array('foo'=>'bar','fruits'=>'apple');
        $req = new HttpPostRequest($url,$post_data);
        
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