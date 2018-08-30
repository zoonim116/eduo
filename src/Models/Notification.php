<?php
/**
 * Created by PhpStorm.
 * User: maxiim
 * Date: 8/28/18
 * Time: 10:30 AM
 */

namespace App\Models;


class Notification extends Model
{
    private static $_table = 'notifications';

    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    public static function create($type, $from, $to, $text) {
        $db = self::forge();

        if(in_array(strtolower($type), [self::SUCCESS, self::INFO, self::WARNING, self::ERROR])) {
            $db->insert(self::$_table, [
                'type' => $type,
                'from_user_id' => $from,
                'to_user_id' => $to,
                'text' => $text,
                'created_at' => time()
            ]);
        }
    }

    public static function find($user_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id(n_id)',
            self::$_table.'.type',
            self::$_table.'.from_user_id',
            self::$_table.'.to_user_id',
            self::$_table.'.text',
            self::$_table.'.is_seen',
            self::$_table.'.created_at',
            'user' => [
                'users.firstname',
                'users.lastname',
                'users.id',
                'users.email',
            ],
        ];

        $notifications = $db->select(self::$_table,
                            [
                                "[>]users" => [self::$_table.'.from_user_id' => 'id']
                            ],
                            $columns,
                            [
                                "AND" => [
                                    "to_user_id" => (int)$user_id,
                                ],
                                "ORDER" => ["created_at" => "DESC"],
                            ]);

        if(count($notifications) > 0) {
            $ids = array_column($notifications, 'n_id');
            $db->update(self::$_table, [
                "is_seen" => 1
            ],[
                'id' => $ids
            ]);
        }
        return $notifications;
    }

    public static function get_count_of_unread($user_id) {
        $db = self::forge();

        return  $db->count(self::$_table,
            [
                "AND" => [
                    "to_user_id" => (int)$user_id,
                    "is_seen" => 0
                ],
            ]);
    }

}