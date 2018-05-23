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

    /**
     * Get subscription by user id
     * @param $user_id
     * @return array|bool
     */
    public static function get_by_user($user_id) {
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
        ], $columns, [
            self::$_table.'.user_id' => $user_id,
            'GROUP' => self::$_table.'.text_id',
            'ORDER' => [self::$_table.'.created_at' => 'DESC']
        ]);
    }

    /**
     * Get subscription by id
     * @param $subscription_id
     * @return array|bool|mixed
     */
    public static function get($subscription_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id',
            self::$_table.'.user_id',
            self::$_table.'.text_id',
            self::$_table.'.created_at',
        ];

       return $db->get(self::$_table, $columns, [self::$_table.'.id' => $subscription_id]);
    }

    /**
     * Remove subscription
     * @param $subscription_id
     * @return int
     */
    public static function delete($subscription_id) {
        $db = self::forge();
        return $db->delete(self::$_table, ['id' => $subscription_id])->rowCount();
    }

    /**
     * Check if user subscribe to text
     * @param $user_id
     * @param $text_id
     * @return array|bool|mixed
     */
    public static function isWatching($user_id, $text_id) {
        $db = self::forge();
        return $db->get(self::$_table, [
            "id"
        ], ['AND' => ['user_id' => $user_id, 'text_id' => $text_id]]);
    }

    public static function delete_all($user_id) {
        $db = self::forge();
        return $db->delete(self::$_table, ['user_id' => $user_id])->rowCount();
    }

}