<?php

namespace App;
use App\Models\Notification as Notification_Model;

class Notification
{
    public static function get($user_id) {
        return Notification_Model::find($user_id);
    }

    public static function get_count_of_unread($user_id) {
        return Notification_Model::get_count_of_unread($user_id);
    }

    /**
     * @param $type("success", "info", "warning", "error") - notification type
     * @param $from - user who initiate notification
     * @param $to - user for whom this notification
     * @param $text - text of notification
     */
    public static function create($type, $from, $to, $text) {
        Notification_Model::create($type, $from, $to, $text);
    }
}