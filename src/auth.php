<?php

namespace App;
use App\Models\User;

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
        return 12;
    }

    public function get_user_firstname() {
        return "OK";
    }

    public function user() {
        return User::find_by_id($_SESSION['user']);
    }

}