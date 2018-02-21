<?php

namespace App\Src\Controllers;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends BaseController
{

    public function sign_in($request, $response) {
        $this->title = "Sign In";
        $this->render($response,'user/sign_in.twig');
    }

    public function sign_up($request, $response) {
        $this->title = "Sign Up";
        $this->render($response,'user/sing_up.twig');
    }

}