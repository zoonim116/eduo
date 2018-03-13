<?php

namespace App\Models;


class Highlight extends Model
{
    private static $_table = 'highlights';

    public static function create($user_id, $text, $text_id) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'user_id' => $user_id,
            'highlight' => $text,
            'text_id' => $text_id,
            'created_at' => time()
        ]);
        return $db->id() ? $db->id() : false;
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