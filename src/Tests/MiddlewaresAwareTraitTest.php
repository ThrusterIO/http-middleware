<?php

namespace Thruster\Component\HttpMiddleware\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Thruster\Component\HttpMiddleware\MiddlewaresAwareTrait;

/**
 * Class MiddlewaresAwareTraitTest
 *
 * @package Thruster\Component\HttpMiddleware\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class MiddlewaresAwareTraitTest extends \PHPUnit_Framework_TestCase
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
        $this->middlewares = $this->getMock('Thruster\Component\HttpMiddleware\Middlewares');

        $this->trait = new class {
            use MiddlewaresAwareTrait;
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

        $this->trait->addMiddleware($b)->addMiddleware($c);

        $response = $this->trait->executeMiddlewares($this->request, $this->response, $a);

        $this->assertEquals($this->response, $response);
        $this->assertEquals(['b', 'c', 'a'], $result);
    }

    public function testAdders()
    {
        $this->trait->setMiddlewares($this->middlewares);

        $a = function () {};

        $this->middlewares->expects($this->once())
            ->method('add')
            ->with($a);

        $this->trait->addMiddleware($a);
    }
}
