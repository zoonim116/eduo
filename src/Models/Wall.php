<?php
/**
 * Created by PhpStorm.
 * User: maxiim
 * Date: 7/5/18
 * Time: 11:12 AM
 */

namespace App\Models;


class Wall extends Model {
    private static $_table = 'wall';

    public static function create_post($message, $user_id) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'user_id' => $user_id,
            'message' => $message,
            'created_at' => time()
        ]);
    }

    public static function find($user_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id',
            self::$_table.'.message',
            self::$_table.'.created_at'
        ];
        return $db->select(self::$_table, $columns, [self::$_table.'.user_id' => $user_id]);
    }

    public static function get($id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id',
            self::$_table.'.message',
            self::$_table.'.created_at',
            self::$_table.'.user_id'
        ];
        return $db->get(self::$_table, $columns, [self::$_table.'.id' => $id]);
    }

    public static function delete($id) {
        $db = self::forge();
        return $db->delete(self::$_table, [self::$_table.'.id' => $id]);
    }
}