<?php

namespace App\Models;


class School extends Model
{
    private static $_table = 'schools';

    public static function find_all() {
        $db = self::forge();
        $columns = [
            'id',
            'name'
        ];
        return $db->select(self::$_table, $columns);
    }
}