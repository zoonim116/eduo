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

    public static function get($user_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.user_id',
            self::$_table.'.text_id',
            self::$_table.'.created_at',
            'text' => [
                'texts.repository_id',
                'texts.title',
                'texts.short_description',
                'texts.id(text_id)'
            ],
            'repository' => [
                'repositories.id(repo_id)',
                'repositories.name',
                'repositories.description'
            ]
        ];
        return $db->select(self::$_table, [
            "[>]texts" => ['text_id' => 'id'],
            "[>]repositories" => ['texts.repository_id' => 'id']
        ], $columns, [self::$_table.'.user_id' => $user_id, 'GROUP' => self::$_table.'.text_id', 'ORDER' => [self::$_table.'.created_at' => 'DESC']]);
    }

}