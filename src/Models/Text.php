<?php

namespace App\Models;


class Text extends Model
{
    private static $_table = 'texts';

    /**
     * Create a new text
     * @param array $data
     * @param null $user_id - user_id owner
     * @param int $type  - type of text draft - 1, published - 2
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
            'repository_id' => $data['repository'],
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        return $db->id() ? $db->id() : false;
    }


    public static function get_by_repo($repo_id) {
        $db = self::forge();
        return $db->select(self::$_table, [
            'id',
            'title',
            'short_description',
            'status',
            'updated_at'
        ], ['repository_id' => $repo_id, 'ORDER' => ['updated_at' => 'DESC']]);
    }

    public static function is_owner($text_id, $user_id) {
        $db = self::forge();
        return $db->get(self::$_table, 'user_id', ['id' => $text_id]) === $user_id;
    }

    public static function delete($text_id) {

    }
}