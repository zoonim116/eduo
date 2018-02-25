<?php

namespace App\Middleware;


class FlashMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->view->getEnvironment()->addGlobal('flash', $this->flash->getMessages());
        return $next($request, $response);
    }
}