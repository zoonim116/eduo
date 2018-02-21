<?php

namespace App\Src\Models;

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

    public function sign_up() {
        $res = $this->db->insert("users", [
            'email' => 'maximko91@gmail.com',
            'firstname' => 'Maxim',
            'lastname' => 'Maxim',
            'password' => password_hash('333666', PASSWORD_DEFAULT),
            'created_at' => time(),
        ]);
        echo "<pre>";
        die(var_dump($res->id));
    }


    public function reset_password() {
        //TODO reset password
    }

}