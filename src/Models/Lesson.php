<?php
/**
 * Created by PhpStorm.
 * User: maxiim
 * Date: 9/6/18
 * Time: 2:49 PM
 */

namespace App\Models;


class Lesson extends Model
{
    private static $_table = 'lessons';

    public static function find($id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id(lesson_id)',
            self::$_table.'.note',
            self::$_table.'.repository_id',
            self::$_table.'.datetime',
            self::$_table.'.rating',
            self::$_table.'.user_id',
            self::$_table.'.created_at',
            'user'=> [
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.is_teacher'
            ],
            'repository' => [
                'repositories.name',
                'repositories.id'
            ]
        ];
        return $db->get(self::$_table,
            [
                "[>]users" => ['user_id' => 'id'],
                "[>]repositories" => ['repository_id' => 'id']
            ],
            $columns,
            [
                self::$_table.'.id' => $id
            ]
        );
    }

    public static function find_by_repo($repo_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id(lesson_id)',
            self::$_table.'.note',
            self::$_table.'.datetime',
            self::$_table.'.rating',
            self::$_table.'.created_at',
            'user'=> [
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.is_teacher'
            ],
            'repository' => [
                'repositories.name',
                'repositories.id'
            ]
        ];
        return $db->select(self::$_table,
            [
                "[>]users" => ['user_id' => 'id'],
                "[>]repositories" => ['repository_id' => 'id']
            ],
            $columns,
            [
                self::$_table.'.repository_id' => $repo_id,
                'ORDER' => [
                    self::$_table.'.created_at' => 'DESC'
                ]
            ]
        );
    }

    public static function update($lesson_id, $repo_id, $datetime, $note, $rating) {
        $db = self::forge();
        return $db->update(self::$_table, [
            'datetime' => strtotime($datetime),
            'repository_id' => $repo_id,
            'note' => strip_tags($note),
            'rating' => (int)$rating
        ], [
            'id' => $lesson_id
        ])->rowCount();
    }

    public static function delete($id) {
        $db = self::forge();
        $db->delete(self::$_table, [
           'id' =>  $id
        ]);
    }

    public static function find_by_user_id($repo_id) {

    }

    public static function create($user_id, $repo_id, $datetime, $note, $rating) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'user_id' => $user_id,
            'note' => strip_tags($note),
            'repository_id' => (int)$repo_id,
            'datetime' => strtotime($datetime),
            'rating' => (int)$rating,
            'created_at' => time()
        ]);
        return $db->id() ? $db->id() : false;
    }
}