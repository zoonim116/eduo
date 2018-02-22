<?php

namespace App\Middleware;


class ValidationMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->view->getEnvironment()->addGlobal('errors', array_key_exists('errors', $_SESSION) ? $_SESSION['errors'] : null);
        unset($_SESSION['errors']);
        return $next($request, $response);
    }
}