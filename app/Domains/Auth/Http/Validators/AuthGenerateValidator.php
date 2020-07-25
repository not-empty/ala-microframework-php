<?php

namespace App\Domains\Auth\Http\Validators;

use App\Http\Validators\BaseValidator;

class AuthGenerateValidator extends BaseValidator
{
    /**
     * get rules for this request
     * @return array
     */
    public function getRules()
    {
        return [
            'token' => 'required|string',
            'secret' => 'required|string',
        ];
    }
}
