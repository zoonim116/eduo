<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

use App\Models\Model;

class EmailAvailable extends AbstractRule
{
    public function validate($input)
    {
        return User::is_unique_email();
        echo "<pre>";
        die(var_dump($this->db));
    }
}