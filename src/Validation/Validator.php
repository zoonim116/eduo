<?php

namespace App\Validation;

use Respect\Validation\Validator as Respect;
use Slim\Http\Request;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    protected $errors;

    public function validate(Request $request, array $rules) {
        foreach ($rules as $field => $rule) {
            try {
                if (!is_null($field) && !is_null($rule)) {
                    $rule->setName(ucfirst($field))->assert($request->getParam($field));
                }
            } catch (NestedValidationException $e) {
                $this->errors[$field] = $e->getMessages();
            }
        }

        $_SESSION['errors'] = $this->errors;

        return $this;
    }

    public function failed() {
        return !empty($this->errors);
    }
}