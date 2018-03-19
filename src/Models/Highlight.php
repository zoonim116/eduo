<?php

namespace App\Models;


class Highlight extends Model
{
    private static $_table = 'highlights';

    public static function create($user_id, $text, $text_id) {
        $db = self::forge();
        $db->insert(self::$_table,
            [
                'user_id' => $user_id,
                'highlight' => $text,
                'text_id' => $text_id,
                'created_at' => time()
            ]
        );
        return $db->id() ? $db->id() : false;
    }

    public static function get($text_id, $user_id) {
        $db = self::forge();
        return $db->select(self::$_table,
            [
                'id',
                'created_at',
                'highlight',
            ], [
                "AND" =>
                    [
                        'user_id' => $user_id,
                        'text_id' => $text_id
                    ],
                'ORDER' =>
                    [
                        'created_at' => 'DESC'
                    ]
            ]
        );
    }

    public static function get_by_user($user_id) {
        $db = self::forge();
        return $db->select(self::$_table,
            [
                "[>]texts" => ['text_id' => 'id']
            ],
            [
                self::$_table.'.id(highlight_id)',
                self::$_table.'.created_at',
                self::$_table.'.highlight',
                self::$_table.'.text_id',
                'text' => [
                    'texts.id',
                    'texts.title',
                    'texts.short_description'
                ]
            ],
            [
                self::$_table.'.user_id' => $user_id,
                'ORDER' => [self::$_table.'.created_at' => 'DESC']
            ]
        );
    }
}