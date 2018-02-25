<?php

namespace App\Middleware;


class IsAuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(!$this->auth->check()) {
            $this->flash->addMessage('error', 'Not authorized');
            return $response->withRedirect($this->router->pathFor('sign_in'));
        }
        return $next($request, $response);
    }

}