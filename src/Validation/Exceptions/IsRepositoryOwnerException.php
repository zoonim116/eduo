<?php

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class IsRepositoryOwnerException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'This is not your repo :-(',
        ]
    ];
}