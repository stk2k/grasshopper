<?php
use Grasshopper\curl\CurlRequestHeader;

class CurlRequestHeaderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function test_construct()
    {
        $header = new CurlRequestHeader();
        $reflection = new \ReflectionClass($header);
        $headers = $reflection->getProperty('headers');
        $headers->setAccessible(true);
    
        $headers = $headers->getValue($header);
        
        $this->assertArrayHasKey('Content-Type',$headers);
        $this->assertArrayHasKey('User-Agent',$headers);
        $this->assertArrayHasKey('Accept',$headers);
        $this->assertArrayHasKey('Accept-Language',$headers);
        $this->assertArrayHasKey('Accept-Encoding',$headers);
        $this->assertArrayHasKey('Accept-Charset',$headers);
        $this->assertArrayHasKey('Connection',$headers);
        $this->assertArrayHasKey('Keep-Alive',$headers);
        $this->assertArrayHasKey('Cache-Control',$headers);
        $this->assertArrayHasKey('Pragma',$headers);
    }
    
    public function test_construct_with_options()
    {
        $options = array(
          'foo' => 'bar',
        );
        
        $header = new CurlRequestHeader($options);
        $reflection = new \ReflectionClass($header);
        $headers = $reflection->getProperty('headers');
        $headers->setAccessible(true);
        
        $headers = $headers->getValue($header);
        
        $this->assertArrayHasKey('foo',$headers);
    }
    
    public function test_compile()
    {
        $options = array(
            'foo' => 'bar',
        );
        
        $header = new CurlRequestHeader($options);
        
        $headers = $header->compile();
        foreach ($headers as $header) {
            $this->assertInternalType('string', $header);
            $this->assertGreaterThanOrEqual(0, strpos($header, ':'));
        }
    }


}