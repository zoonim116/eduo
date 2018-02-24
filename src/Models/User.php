<?php

namespace App\Models;

class User extends Model {

    private static $_table = 'users';

    /**
     * @param $data
     * @return bool|int|mixed|string
     */

    public static function sign_up($data) {
        $db =  self::forge();
        $result = $db->insert(self::$_table, [
            'email' => $data['email'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => time(),
        ]);
        return $db->id() ? $db->id() : false;
    }

    public static function is_unique_email() {
            return true;
    }


}