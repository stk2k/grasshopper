<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Grasshopper\Grasshopper;
use Grasshopper\event\SuccessEvent;
use Grasshopper\event\ErrorEvent;

$hopper = new Grasshopper();

$url = 'http://sample.com';

$options = array('verbose'=>true);

$hopper->addGetRequest($url,null,$options);

$result = $hopper->waitForAll();

$res = $result[$url];
if ( $res instanceof SuccessEvent ){
    // success
    $status = $res->getResponse()->getStatusCode();
    $body = $res->getResponse()->getBody();
    echo "success: status=$status" . PHP_EOL;
    echo "request_header:" . $body . PHP_EOL;
    echo "request_header:" . $res->getResponse()->getRequestHeader() . PHP_EOL;
}
elseif ( $res instanceof ErrorEvent ){
    // error
    echo "error: " . $res->getError()->getMessage() . PHP_EOL;
}
