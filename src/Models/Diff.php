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
}