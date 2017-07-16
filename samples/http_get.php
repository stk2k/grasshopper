<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Grasshopper\Grasshopper;
use Grasshopper\event\SuccessEvent;
use Grasshopper\event\ErrorEvent;

$hopper = new Grasshopper();

$url = 'http://sample.com';

$hopper->addGetRequest($url,null);

$result = $hopper->waitForAll();

$res = $result[$url];
if ( $res instanceof SuccessEvent ){
    // success
    $status = $res->getResponse()->getStatusCode();
    $body = $res->getResponse()->getBody();
    echo "success: status=$status" . PHP_EOL;
    echo $body . PHP_EOL;
}
elseif ( $res instanceof ErrorEvent ){
    // error
    echo "error: " . $res->getError()->getMessage() . PHP_EOL;
}
