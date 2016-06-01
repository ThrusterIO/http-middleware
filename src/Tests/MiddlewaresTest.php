<?php

namespace Thruster\Component\HttpMiddleware\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Thruster\Component\HttpMiddleware\Middlewares;

/**
 * Class MiddlewaresTest
 *
 * @package Thruster\Component\HttpMiddleware\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class MiddlewaresTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Middlewares
     */
    protected $middlewares;

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
        $this->middlewares = new Middlewares();

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

            return $next($request, $response);
        };

        $b = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'b';

            return $next($request, $response);
        };

        $c = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'c';

            return $next($request, $response);
        };

        $this->middlewares->add($b)->add($c);

        $response = $this->middlewares->__invoke($this->request, $this->response, $a);

        $this->assertEquals($this->response, $response);
        $this->assertEquals(['b', 'c', 'a'], $result);
    }

    public function testMiddlewaresWithoutNext()
    {
        $result = [];

        $b = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'b';

            return $next($request, $response);
        };

        $c = function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use (&$result) {
            $result[] = 'c';

            return $next($request, $response);
        };

        $this->middlewares->add($b)->add($c);

        $response = $this->middlewares->__invoke($this->request, $this->response);

        $this->assertEquals($this->response, $response);
        $this->assertEquals(['b', 'c'], $result);
    }

    public function testMethods()
    {
        $a = function () {};

        $given = [$a];

        $this->assertCount(0, $this->middlewares->all());
        $this->assertFalse($this->middlewares->has($a));

        $this->middlewares->remove($a);

        $this->middlewares->__construct($given);

        $this->assertEquals($given, $this->middlewares->all());

        $this->assertTrue($this->middlewares->has($a));

        $this->middlewares->remove($a);

        $this->assertCount(0, $this->middlewares->all());
        $this->assertFalse($this->middlewares->has($a));
    }
}
