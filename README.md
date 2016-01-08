# HttpMiddleware Component

[![Latest Version](https://img.shields.io/github/release/ThrusterIO/http-middleware.svg?style=flat-square)]
(https://github.com/ThrusterIO/http-middleware/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)]
(LICENSE)
[![Build Status](https://img.shields.io/travis/ThrusterIO/http-middleware.svg?style=flat-square)]
(https://travis-ci.org/ThrusterIO/http-middleware)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ThrusterIO/http-middleware.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/ThrusterIO/http-middleware)
[![Quality Score](https://img.shields.io/scrutinizer/g/ThrusterIO/http-middleware.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/ThrusterIO/http-middleware)
[![Total Downloads](https://img.shields.io/packagist/dt/thruster/http-middleware.svg?style=flat-square)]
(https://packagist.org/packages/thruster/http-middleware)

[![Email](https://img.shields.io/badge/email-team@thruster.io-blue.svg?style=flat-square)]
(mailto:team@thruster.io)

The Thruster HttpMiddleware Component. PSR-7 based middleware dispatcher

## Install

Via Composer

```bash
$ composer require thruster/http-middleware
```


## Usage

### Middlewares

A simple middlewares concept running all registered middlewares.

```php
<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Thruster\Component\HttpMiddleware\Middlewares;

$middlewares = new Middlewares();

$middlewares->add(
    function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
        // ... Do something cool

        return $next($request, $response);
    }
);

$middlewares->add(new SomeCoolMiddleware());
$middlewares->add([$object, 'execute']);

$response = $middlewares($request, new Response());

$notUsefulMiddleware = [$object, 'execute'];
if ($middlewares->has($notUsefulMiddleware)) {
    $middlewares->remove($notUsefulMiddleware);
}

$response = $middlewares($request, new Response());
```

### Using MiddlewaresAwareTrait Trait

HttpMiddleware provides a simple trait to include middlewares inside your class.

```php
<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Thruster\Component\HttpMiddleware\MiddlewaresAwareTrait;

$application = new class
{
    use MiddlewaresAwareTrait;

    public function __construct()
    {
        $this->addMiddleware(
            function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
                // ... Do something cool

                return $next($request, $response);
            }
        );

        $this->addMiddleware(new SomeCoolMiddleware());
        $this->addMiddleware([$this, 'executeAction']);
    }

    public function executeAction(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // ...
    }

    public function handleRequest(ServerRequestInterface $request)
    {
        $response = new Response();
        
        $response = $this->executeMiddlewares($request, $response);
    }
};


$application->handleRequest(ServerRequest::fromGlobals());
```

### RequestMiddlewares

When you want to have sepparete middlewares before calling controller action and after call to controller.

```php
<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Thruster\Component\HttpMiddleware\RequestMiddlewares;

$middlewares = new RequestMiddlewares();

$middlewares->pre(
    function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
        // ... Do something cool

        return $next($request, $response);
    }
);

$middlewares->post(new SomeCoolMiddleware());

$response = $middlewares($request, new Response(), [$object, 'executeControllerAction']);
``` 

### RequestMiddlewaresAwareTrait

Like MiddlewaresAwareTrait and so the same you can register RequestMiddlewares as a trait.

```php
<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Thruster\Component\HttpMiddleware\RequestMiddlewaresAwareTrait;

$application = new class
{
    use RequestMiddlewaresAwareTrait;

    public function __construct()
    {
        $this->addPreMiddleware(
            function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
                // ... Do something cool

                return $next($request, $response);
            }
        );

        $this->addPostMiddleware(new SomeCoolMiddleware());
    }

    public function executeAction(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // ...
    }

    public function handleRequest(ServerRequestInterface $request)
    {
        $response = new Response();

        $response = $this->executeMiddlewares($request, $response, [$this, 'executeAction']);
    }
};


$application->handleRequest(ServerRequest::fromGlobals());

```


## Testing

```bash
$ composer test
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## License

Please see [License File](LICENSE) for more information.

[FastRoute]: https://github.com/nikic/FastRoute
