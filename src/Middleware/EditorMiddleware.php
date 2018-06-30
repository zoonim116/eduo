<?php

namespace App\Middleware;


class EditorMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $route = $request->getAttribute('route');
        if(isset($route) && $route->getName() == 'text.view') {
            $this->view->getEnvironment()->addGlobal('editor', 'show');
        } else {
            $this->view->getEnvironment()->addGlobal('editor', 'edit');
        }
        return $next($request, $response);
    }
}