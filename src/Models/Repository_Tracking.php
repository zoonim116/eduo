<?php

namespace App\Models;


class Repository_Tracking extends Model
{
    private static $_table = 'repository_tracking';

    /**
     * Add user visit of repo
     * @param $user_id
     * @param $repo_id
     * @return bool|int|mixed|string
     */
    public static function create($user_id, $repo_id) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'repository_id' => $repo_id,
            'user_id' => $user_id,
            'created_at' => time(),
        ]);
        return $db->id() ? $db->id() : false;
    }


    public static function get($subscription_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id',
            self::$_table.'.user_id',
            self::$_table.'.repository_id',
            self::$_table.'.created_at',
        ];
        return $db->get(self::$_table, $columns, [self::$_table.'.id' => $subscription_id]);
    }

    public static function get_by_id($user_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.user_id',
            self::$_table.'.repository_id',
            self::$_table.'.created_at',
            'repository' => [
                'repositories.name',
                'repositories.description'
            ]
        ];
        return $db->select(self::$_table, [
            "[>]repositories" => ['repository_id' => 'id'],
        ], $columns, [self::$_table.'.user_id' => $user_id, 'GROUP' => self::$_table.'.repository_id', 'ORDER' => [self::$_table.'.created_at' => 'DESC']]);
    }

    public static function isWatching($user_id, $repo_id) {
        $db = self::forge();
        return $db->get(self::$_table, [
            "id"
        ], ['AND' => ['user_id' => $user_id, 'repository_id' => $repo_id]]);
    }

    public static function delete($subscription_id) {
        $db = self::forge();
        return $db->delete(self::$_table, ['id' => $subscription_id])->rowCount();
    }
}