<?php

namespace App\Middleware;


class OldInputMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->view->getEnvironment()->addGlobal('fields', array_key_exists('params', $_SESSION) ? $_SESSION['params'] : null);
        $_SESSION['params'] = $request->getParams();
        return $next($request, $response);
    }
}