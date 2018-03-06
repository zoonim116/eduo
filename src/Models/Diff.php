<?php

namespace App\Models;


class Diff extends Model
{
    private static $_table = 'diffs';

    public static function create($text_id, $diff) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'text_id' => $text_id,
            'diff' => $diff,
            'created_at' => time()
        ]);
        return $db->id() ? $db->id() : false;
    }

    public static function get($text_id) {
        $db = self::forge();
        return $db->select(self::$_table, [
            'id',
            'created_at',
            'diff',
        ], ['text_id' => $text_id, 'ORDER' => ['created_at' => 'DESC']]);
    }
}