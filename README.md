Google Analytics Measurement Protocol for Yii2
==============================================

[![Packagist](https://img.shields.io/packagist/l/baibaratsky/yii2-ga-measurement-protocol.svg)](https://github.com/baibaratsky/yii2-ga-measurement-protocol/blob/master/LICENSE.md)
[![Dependency Status](https://www.versioneye.com/user/projects/559663cd6166340022000002/badge.svg?style=flat)](https://www.versioneye.com/user/projects/559663cd6166340022000002)
[![Packagist](https://img.shields.io/packagist/v/baibaratsky/yii2-ga-measurement-protocol.svg)](https://packagist.org/packages/baibaratsky/yii2-ga-measurement-protocol)
[![Packagist](https://img.shields.io/packagist/dt/baibaratsky/yii2-ga-measurement-protocol.svg)](https://packagist.org/packages/baibaratsky/yii2-ga-measurement-protocol)

>Interact with Google Analytics directly. No need of any JS code. Pure server-side.

Full support for all methods of the
[Google Analytics Measurement Protocol](https://developers.google.com/analytics/devguides/collection/protocol/v1/)
is provided.


Installation
------------

**Note:** 
Versions in 1.* range are incompatible with PHP 7.2, use 2.* with Yii 2.0.13+ instead.

1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

   To install, either run
   ```
   $ php composer.phar require baibaratsky/yii2-ga-measurement-protocol:2.0.*
   ```
   or add
   ```
   "baibaratsky/yii2-ga-measurement-protocol": "2.0.*"
   ```
   to the `require` section of your `composer.json` file.

   **For PHP versions prior to 7.2, use a release from 1.\* range:**
   ```
   "baibaratsky/yii2-ga-measurement-protocol": "1.2.*"
   ```

1. Add the component configuration in your `main.php` config file:
   ```php
   'components' => [
       'ga' => [
           'class' => 'baibaratsky\yii\google\analytics\MeasurementProtocol',
           'trackingId' => 'UA-XXXX-Y', // Put your real tracking ID here

           // These parameters are optional:
           'useSsl' => true, // If you’d like to use a secure connection to Google servers
           'overrideIp' => false, // By default, IP is overridden by the user’s one, but you can disable this
           'anonymizeIp' => true, // If you want to anonymize the sender’s IP address
           'asyncMode' => true, // Enables the asynchronous mode (see below)
           'autoSetClientId' => true, // Try to set ClientId automatically from the “ga_” cookie (disabled by default)
       ],
   ],
   ```


Usage
-----
This extension is just a wrapper around the
[Google Analytics Measurement Protocol library for PHP](https://github.com/theiconic/php-ga-measurement-protocol).
`request()` returns a `TheIconic\Tracking\GoogleAnalytics\Analytics` object, so all its methods will work seamlessly.

#### Basic Usage
```php
\Yii::$app->ga->request()
    ->setClientId('12345678')
    ->setDocumentPath('/mypage')
    ->sendPageview();
```

#### Order Tracking with Enhanced E-commerce

```php
$request = \Yii::$app->ga->request();

// Build the order data programmatically, each product of the order included in the payload
// First, general and required hit data
$request->setClientId('12345678');
$request->setUserId('123');

// Then, include the transaction data
$request->setTransactionId('7778922')
    ->setAffiliation('THE ICONIC')
    ->setRevenue(250.0)
    ->setTax(25.0)
    ->setShipping(15.0)
    ->setCouponCode('MY_COUPON');

// Include a product, the only required fields are SKU and Name
$productData1 = [
    'sku' => 'AAAA-6666',
    'name' => 'Test Product 2',
    'brand' => 'Test Brand 2',
    'category' => 'Test Category 3/Test Category 4',
    'variant' => 'yellow',
    'price' => 50.00,
    'quantity' => 1,
    'coupon_code' => 'TEST 2',
    'position' => 2
];

$request->addProduct($productData1);

// You can include as many products as you need, this way
$productData2 = [
    'sku' => 'AAAA-5555',
    'name' => 'Test Product',
    'brand' => 'Test Brand',
    'category' => 'Test Category 1/Test Category 2',
    'variant' => 'blue',
    'price' => 85.00,
    'quantity' => 2,
    'coupon_code' => 'TEST',
    'position' => 4
];

$request->addProduct($productData2);

// Don't forget to set the product action, which is PURCHASE in the example below
$request->setProductActionToPurchase();

// Finally, you need to send a hit; in this example, we are sending an Event
$request->setEventCategory('Checkout')
    ->setEventAction('Purchase')
    ->sendEvent();
```


Asynchronous Mode
-----------------
By default, sending a hit to Google Analytics will be a synchronous request, and it will block the execution of
the script until the latter gets a response from the server or terminates by timeout after 100 seconds (throwing a Guzzle exception).
However, if you turn the asynchronous mode on in the component config, asynchronous non-blocking requests will be used.
```php
'asyncMode' => true,
```
This means that we are sending the request and not waiting for response.
The `TheIconic\Tracking\GoogleAnalytics\AnalyticsResponse` object that you will get back has `null` for HTTP status code.

You can also send an asynchronous request even if you haven’t turned it on in the config. Just call `setAsyncRequest(true)`
before sending the hit:
```php
\Yii::$app->ga->request()
    ->setClientId('12345678')
    ->setDocumentPath('/mypage')
    ->setAsyncRequest(true)
    ->sendPageview();
```
