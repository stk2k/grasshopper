Grasshopper, PHP HTTP Multi Request Client
=======================

Grasshopper is a yet another HTTP Client that makes it easy to send HTTP
requests.


```php
    use Grasshopper\Grasshopper;

    $hopper = new Grasshopper();

    $url = 'http://www.example.org/';

    $hopper->addRequest(new HttpGetRequest($url));

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

## Installing Grasshopper

The recommended way to install Grasshopper is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of Grasshopper:

```bash
composer.phar require stk2k/grasshopper
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update Grasshopper using composer:

 ```bash
composer.phar update
 ```
