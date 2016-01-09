<?php

namespace Thruster\Component\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Middlewares
 *
 * @package Thruster\Component\HttpMiddleware
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class Middlewares
{
    /**
     * @var callable[]
     */
    protected $middlewares;

    public function __construct(array $middlewares = [])
    {
        $this->middlewares = [];

        foreach ($middlewares as $middleware) {
            $this->add($middleware);
        }
    }

    /**
     * @return callable[]
     */
    public function all() : array
    {
        return $this->middlewares;
    }

    /**
     * @param callable $middleware
     *
     * @return Middlewares
     */
    public function add(callable $middleware) : self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     * @param callable $middleware
     *
     * @return bool
     */
    public function has(callable $middleware) : bool
    {
        return false !== array_search($middleware, $this->middlewares, true);
    }

    /**
     * @param callable $middleware
     *
     * @return Middlewares
     */
    public function remove(callable $middleware) : self
    {
        $key = array_search($middleware, $this->middlewares, true);

        if (false === $key) {
            return $this;
        }

        unset($this->middlewares[$key]);

        return $this;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) : ResponseInterface {
        $dispatcher = new class($this->middlewares)
        {
            /**
             * @var callable[]
             */
            protected $middlewares;

            public function __construct($middlewares)
            {
                $this->middlewares = $middlewares;
            }

            public function __invoke(
                ServerRequestInterface $request,
                ResponseInterface $response,
                callable $next = null
            ) : ResponseInterface {
                /** @var callable $middleware */
                $middleware = array_shift($this->middlewares);

                if (null !== $middleware) {
                    $response = $middleware($request, $response, $this);
                }

                if (null === $next) {
                    return $response;
                }

                $skip = function (ServerRequestInterface $request, ResponseInterface $response) {
                    return $response;
                };

                return $next($request, $response, $skip);
            }
        };

        return $dispatcher($request, $response, $next);
    }
}
