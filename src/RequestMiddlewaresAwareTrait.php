<?php

namespace Thruster\Component\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait RequestMiddlewaresAwareTrait
 *
 * @package Thruster\Component\HttpMiddleware
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
trait RequestMiddlewaresAwareTrait
{
    /**
     * @var RequestMiddlewares
     */
    protected $requestMiddlewares;

    /**
     * @return RequestMiddlewares
     */
    public function getRequestMiddlewares() : RequestMiddlewares
    {
        if ($this->requestMiddlewares) {
            return $this->requestMiddlewares;
        }

        $this->requestMiddlewares = new RequestMiddlewares();

        return $this->requestMiddlewares;
    }

    /**
     * @param RequestMiddlewares $requestMiddlewares
     *
     * @return $this
     */
    public function setRequestMiddlewares(RequestMiddlewares $requestMiddlewares)
    {
        $this->requestMiddlewares = $requestMiddlewares;

        return $this;
    }

    /**
     * @param callable $middleware
     *
     * @return $this
     */
    public function addPreMiddleware(callable $middleware)
    {
        $this->getRequestMiddlewares()->pre($middleware);

        return $this;
    }

    /**
     * @param callable $middleware
     *
     * @return $this
     */
    public function addPostMiddleware(callable $middleware)
    {
        $this->getRequestMiddlewares()->post($middleware);

        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $requestHandler
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function executeMiddlewares(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $requestHandler = null,
        callable $next = null
    ) : ResponseInterface {
        return $this->getRequestMiddlewares()->__invoke($request, $response, $requestHandler, $next);
    }
}
