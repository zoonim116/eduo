<?php

namespace App\Models;

class Category extends Model {
    private static $_table = 'category';

    public static function get($category_id) {
        $db = self::forge();
    }

    public static function get_all() {
        $db = self::forge();
        $columns = [
            'id',
            'name'
        ];
        return $db->select('categories', $columns);
    }

    public static function get_with_text_count() {
        $db = self::forge();
        return $db->query('SELECT c.id, c.name, (SELECT COUNT(*) FROM texts WHERE category_id = c.id) text_count FROM categories as c')->fetchAll();
    }
}