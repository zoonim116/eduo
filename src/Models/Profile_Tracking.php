<?php

namespace App\Models;


class Profile_Tracking extends Model
{
    private static $_table = 'profile_tracking';

    public static function create($user_id, $profile_id) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'profile_id' => $profile_id,
            'user_id' => $user_id,
            'created_at' => time(),
        ]);
        return $db->id() ? $db->id() : false;
    }

    /**
     * Get track info by id
     * @param $subscription_id
     * @return array|bool|mixed
     */
    public static function get($subscription_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id',
            self::$_table.'.user_id',
            self::$_table.'.profile_id',
            self::$_table.'.created_at',
        ];
        return $db->get(self::$_table, $columns, [self::$_table.'.id' => $subscription_id]);
    }


    /**
     * Check if user is subscribed to profile
     * @param $user_id
     * @param $profile_id
     * @return array|bool|mixed
     */
    public static function isWatching($user_id, $profile_id) {
        $db = self::forge();
        return $db->get(self::$_table, [
            "id"
        ], ['AND' => ['user_id' => $user_id, 'profile_id' => $profile_id]]);
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

    public static function delete_all($user_id) {
        $db = self::forge();
        return $db->delete(self::$_table, ['user_id' => $user_id])->rowCount();
    }

}