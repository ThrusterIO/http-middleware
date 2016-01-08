<?php

namespace Thruster\Component\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestMiddlewares
 *
 * @package Thruster\Component\HttpMiddleware
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class RequestMiddlewares
{
    /**
     * @var Middlewares
     */
    protected $preMiddlewares;

    /**
     * @var Middlewares
     */
    protected $postMiddlewares;

    /**
     * @return Middlewares
     */
    public function getPreMiddlewares() : Middlewares
    {
        if ($this->preMiddlewares) {
            return $this->preMiddlewares;
        }
        
        $this->preMiddlewares = new Middlewares()  ;
        
        return $this->preMiddlewares;
    }

    /**
     * @param Middlewares $preMiddlewares
     *
     * @return $this
     */
    public function setPreMiddlewares(Middlewares $preMiddlewares)
    {
        $this->preMiddlewares = $preMiddlewares;

        return $this;
    }

    /**
     * @return Middlewares
     */
    public function getPostMiddlewares() : Middlewares
    {
        if ($this->postMiddlewares) {
            return $this->postMiddlewares;
        }
        
        $this->postMiddlewares = new Middlewares();
        
        return $this->postMiddlewares;
    }

    /**
     * @param Middlewares $postMiddlewares
     *
     * @return $this
     */
    public function setPostMiddlewares(Middlewares $postMiddlewares)
    {
        $this->postMiddlewares = $postMiddlewares;

        return $this;
    }

    /**
     * @param callable $middleware
     *
     * @return $this
     */
    public function pre(callable $middleware)
    {
        $this->getPreMiddlewares()->add($middleware);

        return $this;
    }

    /**
     * @param callable $middleware
     *
     * @return $this
     */
    public function post(callable $middleware)
    {
        $this->getPostMiddlewares()->add($middleware);

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
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $requestHandler = null,
        callable $next = null
    ) : ResponseInterface
    {
        return $this->preMiddlewares->__invoke(
            $request,
            $response,
            function (ServerRequestInterface $request, ResponseInterface $response) use ($requestHandler, $next) {
                if (null !== $requestHandler) {
                    $response = $requestHandler($request, $response);
                }

                return $this->postMiddlewares->__invoke($request, $response, $next);
            }
        );
    }
}
