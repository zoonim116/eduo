<?php

namespace App\Models;


class Text_Tracking extends Model
{
    private static $_table = 'text_tracking';

    /**
     * Add user visit of text
     * @param $user_id
     * @param $repo_id
     * @return bool|int|mixed|string
     */
    public static function create($user_id, $text_id) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'text_id' => $text_id,
            'user_id' => $user_id,
            'created_at' => time(),
        ]);
        return $db->id() ? $db->id() : false;
    }

}