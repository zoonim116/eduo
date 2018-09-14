<?php

namespace App\Models;


class Courses extends Model
{
    private static $_table = 'courses';

    public static function create($user_id, $date_from, $date_to, $number_of_lessons, $number_of_students, $school_id, $repository_id, $note) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'datetime_from' => strtotime($date_from),
            'datetime_to' => strtotime($date_to),
            'number_of_lessons' => intval($number_of_lessons),
            'number_of_students' => intval($number_of_students),
            'school_id' => intval($school_id),
            'repository_id' => intval($repository_id),
            'note' => strip_tags($note),
            'user_id' => $user_id,
            'created_at' => time()
        ]);
        return $db->id() ? $db->id() : false;
    }

    public static function find($user_id) {
        $db = self::forge();
        $columns = [
            self::$_table.'.id(course_id)',
            self::$_table.'.datetime_from',
            self::$_table.'.datetime_to',
            self::$_table.'.number_of_lessons',
            self::$_table.'.number_of_students',
            self::$_table.'.note',
            self::$_table.'.created_at',
            'school' => [
                'schools.id(school_id)',
                'schools.name(school_name)'
            ],
            'repository' => [
                'repositories.id(repo_id)',
                'repositories.name(repo_name)'
            ]

        ];
        return $db->select(self::$_table,
            [
                "[>]schools" => ['school_id' => 'id'],
                "[>]repositories" => ['repository_id' => 'id']
            ],$columns, [
                self::$_table.'.user_id' => $user_id,
                'ORDER' => [
                    self::$_table.'.created_at' => 'DESC'
                ]
            ]);
    }

    public static function get($id) {
        $db = self::forge();
        $columns = [
            'id',
            'datetime_from',
            'datetime_to',
            'number_of_lessons',
            'number_of_students',
            'school_id',
            'repository_id',
            'note',
            'user_id'
        ];
        return $db->get(self::$_table, $columns, [
            self::$_table.'.id' => $id
        ]);
    }

    public static function update($course_id, $date_from, $date_to, $number_of_lessons, $number_of_students, $school_id, $repository_id, $note) {
        $db = self::forge();
        return $db->update(self::$_table, [
            'datetime_from' => strtotime($date_from),
            'datetime_to' => strtotime($date_to),
            'number_of_lessons' => intval($number_of_lessons),
            'number_of_students' => intval($number_of_students),
            'school_id' => intval($school_id),
            'repository_id' => intval($repository_id),
            'note' => strip_tags($note),
        ], [
            'id' => $course_id
        ])->rowCount();
    }

    public static function delete($course_id) {
        $db = self::forge();
        return $db->delete(self::$_table, [
            'id' => $course_id
        ]);
    }
}