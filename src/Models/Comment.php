<?php

namespace App\Models;


class Comment extends Model
{
    private static $_table = 'comments';


    public static function create($user_id, $comment, $text, $text_id) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'user_id' => $user_id,
            'comment' => strip_tags($comment),
            'text' => $text,
            'text_id' => $text_id,
            'created_at' => time()
        ]);
        return $db->id() ? $db->id() : false;
    }

    public static function get_all($text_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id',
            self::$_table.'.comment',
            self::$_table.'.text',
            self::$_table.'.text_id',
            self::$_table.'.created_at',
            'user' => [
                'users.firstname',
                'users.lastname',
                'users.id',
                'users.email',
            ]
        ];
        return $db->select(self::$_table, [
            "[>]users" => ['user_id' => 'id'],
        ], $columns, [self::$_table.'.text_id' => $text_id, 'ORDER' => [self::$_table.'.created_at' => 'DESC']]);
    }

    public static function get($text_id, $user_id) {
        $db = self::forge();
        return $db->select(self::$_table, [
            'id',
            'created_at',
            'comment',
        ], ["AND" => ['user_id' => $user_id, 'text_id' => $text_id],  'ORDER' => ['created_at' => 'DESC']]);
    }
}