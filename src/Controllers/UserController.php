<?php

namespace App\Controllers;
use App\Auth;
use App\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class UserController extends BaseController
{

    public function sign_in($request, $response) {
        if ($request->isPost()) {
            $validation = $this->validator->validate($request, [
                'email' => v::noWhitespace()->notEmpty()->email(),
                'password' => v::notEmpty()->length(6, 25, true),
            ]);
            if($validation->failed()) {
                return $response->withRedirect($this->router->pathFor('sign_in'));
            }
            $auth = $this->auth->attempt($request->getParam('email', false), $request->getParam('password', false));
            if(!$auth) {

                return $response->withRedirect($this->router->pathFor('sign_in'));
            } else {
                return $response->withRedirect($this->router->pathFor('dashboard'));
            }
        }
        $this->title = "Sign In";
        $this->render($response,'user/sign_in.twig');
    }

    public function sign_up($request, $response) {
        if($request->isPost()) {
            $validation = $this->validator->validate($request, [
                'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
                'firstname' => v::noWhitespace()->notEmpty()->alpha(),
                'lastname' => v::noWhitespace()->notEmpty()->alpha(),
                'password' => v::notEmpty()->length(6, 25, true)->equals($request->getParam('verify')),
                'verify' => v::notEmpty()->length(6, 25, true)->equals($request->getParam('password')),
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

    public function logout($request, $response) {
        unset($_SESSION['user']);
        return $response->withRedirect($this->router->pathFor('sign_in'));
    }

}