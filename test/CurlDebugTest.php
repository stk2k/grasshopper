<?php
namespace Grasshopper;

use \Grasshopper\debug\CurlDebug;

class CurlDebugTest extends \PhpUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testPrintOptions()
    {
        $data = array(
            CURLOPT_URL => 1,
            CURLOPT_HEADER => 'bison',
            CURLOPT_POST => true,
        );

        ob_start();
        CurlDebug::printOptions($data);
        $actual = ob_get_clean();
        $expected = 'CURLOPT_URL => 1' . PHP_EOL;
        $expected .= 'CURLOPT_HEADER => bison' . PHP_EOL;
        $expected .= 'CURLOPT_POST => 1' . PHP_EOL;

        $this->assertEquals($expected, $actual);
    }

    public function testGetOptionName()
    {
        $actual = CurlDebug::getOptionName(CURLOPT_URL);
        $expected = 'CURLOPT_URL';

        $this->assertEquals($expected, $actual);

        $actual = CurlDebug::getOptionName(CURLOPT_HEADER);
        $expected = 'CURLOPT_HEADER';

        $this->assertEquals($expected, $actual);

        $actual = CurlDebug::getOptionName(CURLOPT_POST);
        $expected = 'CURLOPT_POST';

        $this->assertEquals($expected, $actual);
    }
}