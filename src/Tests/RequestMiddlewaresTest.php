<?php

namespace Thruster\Component\HttpMiddleware\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Thruster\Component\HttpMiddleware\RequestMiddlewares;

/**
 * Class RequestMiddlewaresTest
 *
 * @package Thruster\Component\HttpMiddleware\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class RequestMiddlewaresTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestMiddlewares
     */
    protected $middlewares;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pre;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $post;

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
        $this->middlewares = new RequestMiddlewares();
        
        $this->pre = $this->getMock('\Thruster\Component\HttpMiddleware\Middlewares');
        $this->post = $this->getMock('\Thruster\Component\HttpMiddleware\Middlewares');

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

        $this->middlewares->pre($b)->pre($c)->pre($a)->post($e)->post($f)->post($d);

        $response = $this->middlewares->__invoke($this->request, $this->response, $h, $g);

        $this->assertEquals($this->response, $response);
        $this->assertEquals(['b', 'c', 'a', 'h', 'e', 'f', 'd', 'g'], $result);
    }

    public function testAdders()
    {
        $a = function () {};
        $b = function () {};

        $this->middlewares->setPreMiddlewares($this->pre);
        $this->middlewares->setPostMiddlewares($this->post);

        $this->pre->expects($this->once())
            ->method('add')
            ->with($a);

        $this->post->expects($this->once())
            ->method('add')
            ->with($b);

        $this->middlewares->pre($a);
        $this->middlewares->post($b);
    }
}
