<?php

namespace App\Middleware;


class CsrfViewMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->view->getEnvironment()->addGlobal('csrf', [
            'field' => '
                <input type="hidden" name="'. $this->csrf->getTokenNameKey() .'" value="'. $this->csrf->getTokenName() .'"/>
                <input type="hidden" name="'. $this->csrf->getTokenValueKey() .'" value="'. $this->csrf->getTokenValue() .'"/>
            ',
        ]);

        return $next($request, $response);
    }
}