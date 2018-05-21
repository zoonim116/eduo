<?php

namespace App\Controllers;
use App\Auth;
use App\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Respect\Validation\Validator as v;

class UserController extends BaseController
{

    public function sign_in(Request $request, Response $response, $args) {

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
                $this->flash->addMessage('error', "There is no user with such email/password");
                return $response->withRedirect($this->router->pathFor('sign_in'));
            } else {
                return $response->withRedirect($this->router->pathFor('dashboard'));
            }
        }
        $this->title = "Sign In";
        $this->render($response,'user/sign_in.twig');
    }

    public function sign_up(Request $request, Response $response, $args) {
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

    public function profile(Request $request, Response $response, $args) {

    }

    public function callback(Request $request, Response $response, $args) {
        $fb = new Facebook([
            'app_id' => getenv('FACEBOOK_APP_ID'),
            'app_secret' => getenv('FACEBOOK_APP_SECRET'),
            'default_graph_version' => getenv('FACEBOOK_GRAPH_VERSION')
        ]);
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
            $oAuth2Client = $fb->getOAuth2Client();
            // Exchanges a short-lived access token for a long-lived one
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            $fbData = $fb->get('/me?fields=email,picture,short_name,id,first_name,last_name', $longLivedAccessToken);

        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        $profile = $fbData->getGraphUser();

        User::update($this->auth->get_user_id(), [
            'firstname' => $profile->getFirstName(),
            'lastname' => $profile->getLastName(),
            'email' => $profile->getEmail(),
            'fb_user_id' => $profile->getId(),
            'fb_access_token' => serialize($longLivedAccessToken),
        ]);
        return $response->withRedirect($this->router->pathFor('user.settings'));
    }

    public function settings(Request $request, Response $response, $args) {
        $user = User::find_by_id($this->auth->get_user_id());
        $fb = new Facebook([
            'app_id' => getenv('FACEBOOK_APP_ID'),
            'app_secret' => getenv('FACEBOOK_APP_SECRET'),
            'default_graph_version' => getenv('FACEBOOK_GRAPH_VERSION')
        ]);
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email','user_photos'];
        $uri = $request->getUri();
        $loginUrl = $helper->getLoginUrl($uri->getScheme()."://" . $uri->getHost(). "/user/callback", $permissions);
        if($request->isPost()) {
            $passwordRules = [];
            if(!empty($request->getParam('new_password')) || !empty($request->getParam('confirm_password'))) {
                $passwordRules = [
                    'new_password' => v::notEmpty()->length(6, 25, true)->equals($request->getParam('new_password')),
                    'confirm_password' => v::notEmpty()->length(6, 25, true)->equals($request->getParam('confirm_password')),
                ];
            }
            $validation = $this->validator->validate($request, array_merge([
                'firstname' => v::noWhitespace()->notEmpty()->alpha(),
                'lastname' => v::noWhitespace()->notEmpty()->alpha(),
            ], $passwordRules));
            if ($validation->failed()) {
                $this->flash->addMessage('error', "There is some errors, try again");
                return $response->withRedirect($this->router->pathFor('user.settings'));
            }
            User::update_user_settings($this->auth->get_user_id(), $request->getParams());
            $this->flash->addMessage('success', "Settings was updated");
                return $response->withRedirect($this->router->pathFor('user.settings'));
        }
        $this->title = 'User settings';
        $this->render($response, 'user/settings.twig', ['user' => $user, 'fb_url' => $loginUrl]);
    }

    public function logout(Request $request, Response $response, $args) {
        unset($_SESSION['user']);
        return $response->withRedirect($this->router->pathFor('sign_in'));
    }

}