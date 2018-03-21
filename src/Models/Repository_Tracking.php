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


    public static function get($user_id) {
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

    public static function isWatching($user_id, $repo_id);

}