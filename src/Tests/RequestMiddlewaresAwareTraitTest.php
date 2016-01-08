<?php

namespace Thruster\Component\HttpMiddleware\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Thruster\Component\HttpMiddleware\RequestMiddlewaresAwareTrait;

/**
 * Class RequestMiddlewaresAwareTraitTest
 *
 * @package Thruster\Component\HttpMiddleware\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class RequestMiddlewaresAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $middlewares;

    protected $trait;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function setUp()
    {
        $this->middlewares = $this->getMock('\Thruster\Component\HttpMiddleware\RequestMiddlewares');

        $this->trait = new class {
            use RequestMiddlewaresAwareTrait;
        };

        $this->request = $this->getMockForAbstractClass('\Psr\Http\Message\ServerRequestInterface');
        $this->response = $this->getMockForAbstractClass('\Psr\Http\Message\ResponseInterface');
    }

    public function testMiddlewares()
    {
        $result = [];

        $a = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'a';

            if (null !== $next) {
                return $next($request, $response);
            }

            return $response;
        };

        $b = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'b';

            if (null !== $next) {
                return $next($request, $response);
            }

            return $response;
        };

        $c = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'c';

            if (null !== $next) {
                return $next($request, $response);
            }

            return $response;
        };

        $d = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'd';

            if (null !== $next) {
                return $next($request, $response);
            }

            return $response;
        };

        $e = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'e';

            if (null !== $next) {
                return $next($request, $response);
            }

            return $response;
        };

        $f = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'f';

            if (null !== $next) {
                return $next($request, $response);
            }

            return $response;
        };

        $g = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'g';

            if (null !== $next) {
                return $next($request, $response);
            }

            return $response;
        };

        $h = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'h';

            if (null !== $next) {
                return $next($request, $response);
            }

            return $response;
        };

        $this->trait->addPreMiddleware($b)->addPreMiddleware($c)->addPreMiddleware($a)
            ->addPostMiddleware($e)->addPostMiddleware($f)->addPostMiddleware($d);

        $response = $this->trait->executeMiddlewares($this->request, $this->response, $h, $g);

        $this->assertEquals($this->response, $response);
        $this->assertEquals(['b', 'c', 'a', 'h', 'e', 'f', 'd', 'g'], $result);
    }

    public function testAdders()
    {
        $a = function () {};
        $b = function () {};

        $this->trait->setRequestMiddlewares($this->middlewares);

        $this->middlewares->expects($this->once())
            ->method('pre')
            ->with($a);

        $this->middlewares->expects($this->once())
            ->method('post')
            ->with($b);

        $this->trait->addPreMiddleware($a);
        $this->trait->addPostMiddleware($b);
    }
}
