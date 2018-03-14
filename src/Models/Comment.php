<?php

namespace App\Models;


class Comment extends Model
{
    private static $_table = 'comments';


    public static function create($user_id, $comment, $text_id) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'user_id' => $user_id,
            'commment' => $comment,
            'text_id' => $text_id,
            'created_at' => time()
        ]);
        return $db->id() ? $db->id() : false;
    }

    public static function get_all($text_id) {

    }

    public static function get($text_id, $user_id) {
        $db = self::forge();
        return $db->select(self::$_table, [
            'id',
            'created_at',
            'highlight',
        ], ["AND" => ['user_id' => $user_id, 'text_id' => $text_id],  'ORDER' => ['created_at' => 'DESC']]);
    }
}