<?php

namespace fortress\core\middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;

abstract class Middleware {

    private ServerRequestInterface $request;

    protected function __construct(ServerRequestInterface $request) {
        $this->request = $request;
    }

    public function handle(Closure $next) {
        if ($this->check($this->request)) {
            return $next();
        }
        return $this->denyResponse($this->request);
    }

    protected abstract function check(ServerRequestInterface $request);

    protected abstract function denyResponse(ServerRequestInterface $request);
}