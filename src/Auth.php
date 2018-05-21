<?php

namespace App;
use App\Models\User;
use Facebook\Facebook;

class Auth
{

    public function attempt($email, $password) {
        $user = User::find_by_email($email);
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['id'];
            return true;
        }

        return false;
    }

    public function check() {
        return isset($_SESSION['user']);
    }

    public function logout() {

    }

    public function get_user_id() {
        return isset($_SESSION['user']) ? $_SESSION['user'] : false ;
    }

    public function get_user_firstname() {
        $user = User::find_by_id($_SESSION['user']);
        return isset($user) ? $user['firstname'] : false;
    }

    public function user() {
        if(isset($_SESSION['user'])) {
            return User::find_by_id($_SESSION['user']);
        }
        return false;
    }

    public function get_user_avatar($user_id, $size) {
        $user = User::find_by_id($user_id);
        if($user['fb_access_token']) {
            $longLivedAccessToken = unserialize($user['fb_access_token']);

            $fb = new Facebook([
                'app_id' => getenv('FACEBOOK_APP_ID'),
                'app_secret' => getenv('FACEBOOK_APP_SECRET'),
                'default_graph_version' => getenv('FACEBOOK_GRAPH_VERSION')
            ]);
             $fbPictures = $fb->get('/me/picture?redirect=0&height='.$size, $longLivedAccessToken);
             $picture = $fbPictures->getGraphUser();
             return $picture['url'];
        } else {
            return Helper::get_gravatar($user['email'], $size);
        }
    }

}