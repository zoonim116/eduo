<?php

namespace App\Models;


class Text extends Model
{
    private static $_table = 'texts';

    /**
     * Create a new text
     * @param array $data
     * @param null $user_id - user_id owner
     * @param int $type Type of text draft - 1, published - 2
     * @return bool|int|mixed|string
     */
    public static function create($data, $user_id,  $status = 2) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'text' => $data['text'],
            'status' => $status,
            'user_id' => $user_id,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        return $db->id() ? $db->id() : false;
    }
}