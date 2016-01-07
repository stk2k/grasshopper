<?php
namespace Grasshopper;

use \Grasshopper\CurlRequest;

class GrasshopperTest extends \PhpUnit_Framework_TestCase
{
    public function testMultiRequest()
    {
        $options = [
            'complete' => function(CurlResponse $response){
                echo "[success]" . PHP_EOL;
                echo "- URL:" . $response->getRequestUrl() . PHP_EOL;
                echo "- effective URL:" . $response->getEffectiveUrl() . PHP_EOL;
                echo "- charset:" . $response->getCharset() . PHP_EOL;
                echo "- HTML:" . PHP_EOL;
                echo $response->getBody(). PHP_EOL;

            },
            'error' => function(CurlError $error){
                echo "[failed]" . PHP_EOL;
                echo "- URL:" . $error->getRequestUrl() . PHP_EOL;

            },
        ];

        $hopper = new Grasshopper();


        $hopper->addRequest(new CurlRequest('http://localhost/test1.html',$options));

        $hopper->perform();
        $result = $hopper->waitForAll();

        echo "finished." . PHP_EOL;

    }
}