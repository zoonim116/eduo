<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use App\Models\Repository;

class IsRepositoryOwner extends AbstractRule
{
    public function validate($input)
    {
        return Repository::is_owner($input, $_SESSION['user']);
    }
}