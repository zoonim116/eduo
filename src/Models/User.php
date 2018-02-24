<?php

namespace App\Models;

class User extends Model {

    private static $_table = 'users';

    /**
     * Sign up user
     * @param $data
     * @return bool|int|mixed|string
     */

    public static function sign_up($data) {
        $db =  self::forge();
        $db->insert(self::$_table, [
            'email' => $data['email'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => time(),
        ]);
        return $db->id() ? $db->id() : false;
    }

    /**
     * Check if the email exists
     * @param $email
     * @return bool
     */
    public static function is_unique_email($email) {
        $db = self::forge();
        return $db->count(self::$_table, ['email' => $email]) === 0;
    }

    /**
     * Find user by email
     * @param $email
     * @return array|bool|mixed
     */
    public static function find_by_email($email) {
        $db = self::forge();
        return $db->get(self::$_table,'*', ['email =' => $email]);
    }

    /**
     * Find user by id
     * @param $id
     * @return array|bool|mixed
     */
    public static function find_by_id($id) {
        $db = self::forge();
        return $db->get(self::$_table, '*', ['id =' => $id]);
    }

}