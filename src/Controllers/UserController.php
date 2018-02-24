<?php

namespace App\Controllers;
use App\Models\User;
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
                'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
                'firstname' => v::noWhitespace()->notEmpty()->alpha(),
                'lastname' => v::noWhitespace()->notEmpty()->alpha(),
                'password' => v::notEmpty()->equals($request->getParam('verify')),
                'verify' => v::notEmpty()->equals($request->getParam('password')),
            ]);
            if ($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('sign_up'));
            }
            $user = User::sign_up($request->getParams());
            if ($user) {
                return $response->withRedirect($this->router->pathFor('sign_in'));
            }
        }

        $this->title = "Sign Up";
        $this->render($response,'user/sing_up.twig');
    }

}