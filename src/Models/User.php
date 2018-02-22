<?php

namespace App\Models;

use Medoo\Medoo;


class User
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function sign_in() {
        $user = $this->db->get('users', '*', [
            "id[=]" => 1
        ]);
        echo "<pre>";
        die(var_dump($user));
    }

    public function sign_up($data) {
        $res = $this->db->insert("users", [
            'email' => $data['email'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => time(),
        ]);
    }

    public function reset_password() {
        //TODO reset password
    }

}