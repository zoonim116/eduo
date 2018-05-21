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
            'is_teacher' => isset($data['is_teacher']) ? 1 : 0,
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

    /**
     * Update user profile settings
     * @param $id
     * @param $data
     */
    public static function update_user_settings($id, $data) {
        $db = self::forge();
        $password_field = [];
        if(!empty($data['new_password'])) {
            $password_field = [
              'password' =>  password_hash($data['new_password'], PASSWORD_DEFAULT),
            ];
        }
        $db->update(self::$_table, array_merge([
            'firstname' => $data['firstname'],
            'lastname'=> $data['lastname'],
            'is_teacher' => isset($data['is_teacher']) ? 1 : 0,
        ], $password_field), [
            'id[=]' => $id
        ]);
    }

    public static function update($id, $data) {
        $db = self::forge();
        $db->update(self::$_table, $data,
            [
                'id[=]' => $id
            ]
        );
    }

}