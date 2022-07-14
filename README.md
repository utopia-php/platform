# Utopia Platform

[![Build Status](https://travis-ci.org/utopia-php/platform.svg?branch=master)](https://travis-ci.com/utopia-php/platform)
![Total Downloads](https://img.shields.io/packagist/dt/utopia-php/platform.svg)
[![Discord](https://img.shields.io/discord/564160730845151244?label=discord)](https://appwrite.io/discord)

An object oriented way of writing Applications using Utopia libraries

## Getting Started

This library contains abstract classes that assist in implementing services and actions for Utopia http framework and CLI. You must implement `Platform`, `Service` and `Action` classes to build your application.

## Example

Install using composer

```
composer require utopia-php/config
```

Implementing a HTTP services using platform.

```php
// Action

<?php

use Utopia\Platform\Action;

class HelloWorldAction extends Action
{
    public function __construct()
    {
        $this->httpPath = '/hello';
        $this->httpMethod = 'GET';
        $this->inject('response');
        $this->callback(fn ($response) => $this->action($response));
    }

    public function action($response)
    {
        $response->send('Hello World!');
    }
}

// service

use Utopia\Platform\Service;

class HelloWorldService extends Service
{
    public function __construct()
    {
        $this->type = Service::TYPE_HTTP;
        $this->addAction('hello', new HelloWorldAction());
    }
}

// Platform

use Utopia\Platform\Platform;

class HelloWorldPlatform extends Platform
{
    public function __construct()
    {
        $this->addService('helloService', new HelloWorldService());
    }
}

// Using platform to initialize http service

$platform = new HelloWorldPlatform();
$platform->init('http');

```

## System Requirements

Utopia Framework requires PHP 8.0 or later. We recommend using the latest PHP version whenever possible.

## Authors

**Damodar Lohani**

+ [https://twitter.com/lohanidamodar](https://twitter.com/lohanidamodar)
+ [https://github.com/lohanidamodar](https://github.com/lohanidamodar)

## Copyright and license

The MIT License (MIT) [http://www.opensource.org/licenses/mit-license.php](http://www.opensource.org/licenses/mit-license.php)
