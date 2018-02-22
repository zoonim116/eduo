<?php

namespace App\Controllers;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class UserController extends BaseController
{

    public function sign_in($request, $response) {
        $this->title = "Sign In";
        $this->render($response,'user/sign_in.twig');
    }

    public function sign_up($request, $response) {
        if($request->isPost()) {
            $validation = $this->validator->validate($request, [
                'email' => v::noWhitespace()->notEmpty()->email(),
                'firstname' => v::noWhitespace()->notEmpty()->alpha(),
                'lastname' => v::noWhitespace()->notEmpty()->alpha(),
                'password' => v::notEmpty()->equals('verify'),
                'verify' => v::notEmpty()->equals('password'),
            ]);
            if ($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('sign_up'));
            }
            $this->UserModel->sign_up($request->getParams());
        }

        $this->title = "Sign Up";
        $this->render($response,'user/sing_up.twig');
    }

}