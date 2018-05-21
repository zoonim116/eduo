<?php

namespace App\Middleware;

use App\Auth;
use App\Helper;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->view->getEnvironment()->addGlobal('auth', [
            'check' => $this->auth->check(),
            'user' => $this->auth->user()
        ]);
        $this->view->getEnvironment()->addGlobal('gravatar_x40', $this->auth->get_user_avatar($this->auth->get_user_id(), 40));
        $this->view->getEnvironment()->addGlobal('gravatar_x80', $this->auth->get_user_avatar($this->auth->get_user_id(),  80));
        return $next($request, $response);
    }
}
