<?php

namespace fortress\core\middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;

abstract class Middleware {

    private $request;

    protected function __construct(Request $request) {
        $this->request = $request;
    }

    public function handle(Closure $next) {
        if ($this->check($this->request)) {
            return $next();
        }
        return $this->denyResponse($this->request);
    }

    protected abstract function check(Request $request);

    protected abstract function denyResponse(Request $request);
}