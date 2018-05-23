<?php

namespace App\Models;


class Text extends Model
{
    private static $_table = 'texts';

    /**
     * Create a new text
     * @param array $data
     * @param null $user_id - user_id owner
     * @param int $type  - type of text draft - 1, published - 2
     * @return bool|int|mixed|string
     */
    public static function create($data, $user_id,  $status = 2) {
        $db = self::forge();
        $db->insert(self::$_table, [
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'text' => $data['text'],
            'status' => $status,
            'user_id' => $user_id,
            'repository_id' => $data['repository'],
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        return $db->id() ? $db->id() : false;
    }


    public static function get_by_repo($repo_id) {
        $db = self::forge();
        return $db->select(self::$_table, [
            'id',
            'title',
            'short_description',
            'status',
            'updated_at'
        ], ['repository_id' => $repo_id, 'ORDER' => ['updated_at' => 'DESC']]);
    }

    public static function is_owner($text_id, $user_id) {
        $db = self::forge();
        return $db->get(self::$_table, 'user_id', ['id' => $text_id]) === $user_id;
    }

    public static function get($id) {
        $db = self::forge();
        $columns = [
            'id',
            'title',
            'short_description',
            'text',
            'user_id',
            'status',
            'repository_id',
            'created_at',
            'updated_at'
        ];
        return $db->get(self::$_table, $columns, ['id' => $id]);
    }

    public static function get_with_relations($id) {
        $db = self::forge();
        $columns = [
            'texts.id(text_id)',
            self::$_table.'.title',
            self::$_table.'.short_description',
            self::$_table.'.text',
            self::$_table.'.status',
            self::$_table.'.repository_id',
            self::$_table.'.created_at',
            self::$_table.'.updated_at',
            'user' => [
                'users.firstname',
                'users.lastname',
                'users.id',
                'users.email',
            ],
            'repository' => [
                'repositories.name',
                'repositories.updated_at',
                'repositories.description',
                'repositories.visibility'
            ],
        ];
        return $db->get(self::$_table, [
            "[>]users" => ['user_id' => 'id'],
            "[>]repositories" => ['repository_id' => 'id']
        ], $columns, [self::$_table.'.id' => $id]);
    }

    public static function delete($id) {
        $db = self::forge();
        $db->delete(self::$_table, ['id' => $id]);
    }

    public static function update($id, $data, $status = 2) {
        $db = self::forge();
        $db->update(self::$_table, [
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'text' => rtrim($data['text'],"+"),
            'status' => $status,
            'updated_at' => time()
        ], [
            'id[=]' => $id
        ]);
    }

    public static function get_recent() {

        $db = self::forge();
        $columns = [
            'texts.id(text_id)',
            self::$_table.'.title',
            self::$_table.'.short_description',
            self::$_table.'.text',
            self::$_table.'.status',
            self::$_table.'.repository_id',
            self::$_table.'.created_at',
            self::$_table.'.updated_at',
            'user' => [
                'users.firstname',
                'users.lastname',
                'users.id',
                'users.email',
            ],
            'repository' => [
                'repositories.name',
                'repositories.updated_at',
                'repositories.description',
                'repositories.visibility'
            ],
        ];
        return $db->select(self::$_table, [
            "[>]users" => ['user_id' => 'id'],
            "[>]repositories" => ['repository_id' => 'id']
        ], $columns, ["AND" => [self::$_table.'.status' => 2, 'repositories.visibility' => 2], 'ORDER' => [self::$_table.'.updated_at' => 'DESC'], 'LIMIT' => 12]);

    }

    public static function get_by_user($user_id) {
        $db = self::forge();
        $columns = [
            'texts.id(text_id)',
            self::$_table.'.title',
            self::$_table.'.short_description',
            self::$_table.'.text',
            self::$_table.'.status',
            self::$_table.'.user_id',
            self::$_table.'.repository_id',
            self::$_table.'.created_at',
            self::$_table.'.updated_at',
        ];
        return $db->select(self::$_table, $columns, ["user_id[=]" => $user_id, "status[=]" =>2]);
    }
}