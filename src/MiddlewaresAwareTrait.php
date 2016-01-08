<?php

namespace Thruster\Component\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait MiddlewaresAwareTrait
 *
 * @package Thruster\Component\HttpMiddleware
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
trait MiddlewaresAwareTrait
{
    /**
     * @var Middlewares
     */
    protected $middlewares;

    /**
     * @return Middlewares
     */
    public function getMiddlewares()
    {
        if ($this->middlewares) {
            return $this->middlewares;
        }

        $this->middlewares = new Middlewares();

        return $this->middlewares;
    }

    /**
     * @param Middlewares $middlewares
     *
     * @return $this
     */
    public function setMiddlewares(Middlewares $middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * @param callable $middleware
     *
     * @return $this
     */
    public function addMiddleware(callable $middleware)
    {
        $this->getMiddlewares()->add($middleware);

        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function executeMiddlewares(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) : ResponseInterface {
        return $this->getMiddlewares()->__invoke($request, $response, $next);
    }
}
