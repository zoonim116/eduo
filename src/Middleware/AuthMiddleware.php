<?php

namespace App\Middleware;

use App\Auth;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->view->getEnvironment()->addGlobal('auth', [
            'check' => $this->auth->check(),
            'user' => $this->auth->user()
        ]);
        return $next($request, $response);
    }
}
