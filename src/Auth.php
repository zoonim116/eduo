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
        return Helper::get_user_avatar($user_id, $size);
    }

    public function is_teacher($user_id = false) {
        if ($user_id) {
            $user = User::find_by_id($user_id);
            return $user['is_teacher'];
        } else {
            $user = User::find_by_id($_SESSION['user']);
            return $user['is_teacher'];
        }
    }

}