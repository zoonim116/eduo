<?php

namespace App\Models;
use Medoo\Medoo;

class Model {

    public static $db;

    private function __construct() {}

    public static function forge()
    {
        if(empty(self::$db)) {
            self::$db = new Medoo([
                'database_type' => getenv('DATABASE_TYPE'),
                'database_name' => getenv('DATABASE_NAME'),
                'server' => getenv('DATABASE_SERVER'),
                'username' => getenv('DATABASE_USERNAME'),
                'password' => getenv('DATABASE_PASSWORD')
            ]);
        }
        return self::$db;
    }

    private function __clone() {}


}
