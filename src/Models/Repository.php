<?php

namespace App\Models;


use App\Auth;

class Repository extends Model
{
    private static $_table = 'repositories';

    /**
     * Create a new repository
     * @param $data
     * @param $user_id
     * @return bool|int|mixed|string
     */
    public static function create($data, $user_id) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'name' => $data['name'],
            'description' => $data['description'],
            'visibility' => $data['visibility'],
            'user_id' => $user_id,
            'created_at' => time(),
            'updated_at' => time()
        ]);
        return $db->id() ? $db->id() : false;
    }

    /**
     * Get array of user repositories
     * @param $user_id
     * @param null $visibilty
     * @return array|bool
     */
    public static function get_repositories($user_id, $visibilty = null) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id',
            self::$_table.'.name',
            self::$_table.'.description',
            self::$_table.'.visibility',
            self::$_table.'.created_at',
            self::$_table.'.updated_at',
            'users.email'
        ];
        if($visibilty) {
            $repositories = $db->select(self::$_table, [
                '[><]users' => ['user_id' => 'id']
            ], $columns, ['visibility' => $visibilty, 'ORDER' => ['created_at' => 'DESC']]);
        } else {
            $repositories = $db->select(self::$_table, $columns, ['visibility' => $visibilty, 'ORDER' => ['created_at' => 'DESC']]);
        }
        return $repositories;
    }

    /**
     * Get repository
     * @param $id ID of repository to get data
     * @return array|bool
     */

    public static function get_repository($id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id',
            self::$_table.'.name',
            self::$_table.'.user_id',
            self::$_table.'.description',
            self::$_table.'.visibility',
            self::$_table.'.created_at',
            self::$_table.'.updated_at',
        ];
        return $db->get(self::$_table, $columns, ['id' => $id]);

    }

    public static function delete_repository($id) {
        $db = self::forge();
        $db->delete(self::$_table, ['id' => $id]);
    }

}