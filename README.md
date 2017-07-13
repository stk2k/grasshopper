Grasshopper, PHP HTTP Multi Request Client
=======================

## Description

Grasshopper is a yet another cURL PHP library wchich makes you easy to send HTTP request.
This library can process multiple requests at once.

## Feature

- supports process multiple requests in one call
- easy to use: simple interface
- variety of error handling: both supported procedural or callback

## Demo

```php
use Grasshopper\Grasshopper;
use \Grasshopper\event\SuccessEvent;
use \Grasshopper\event\ErrorEvent;
 
$hopper = new Grasshopper();

$url = 'http://example.com';

$hopper->addRequest($url);

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
 
```

## Usage

1. create grashopper object.
2. add HttpGet/HttpPostRequest to grasshopper object.
3. execute Grasshopper#waitforAll() method.
4. get response from returned array.the key is requested URL.
5. check response object whether SuccessEvent or ErrorEvent.SuccessEvent means request was succeeded, ErrorEvent means failure.
6. you can get response object from SuccessEvent. it provides status code and response body.
7. you can get error information from ErrorEvent. it provides error code and message.

## Requirement

PHP 5.5 or later

## Installing Grasshopper

The recommended way to install Grasshopper is through
[Composer](http://getcomposer.org).

```bash
composer require stk2k/grasshopper
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## License
[MIT](https://github.com/stk2k/grasshopper/blob/master/LICENSE)

## Author

[stk2k](https://github.com/stk2k)

## Disclaimer

This software is no warranty.

We are not responsible for any results caused by the use of this software.

Please use the responsibility of the your self.


