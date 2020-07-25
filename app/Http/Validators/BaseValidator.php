<?php

namespace App\Http\Validators;

use App\Exceptions\Custom\ValidationException;
use Illuminate\Support\Facades\Validator;

abstract class BaseValidator
{
    /**
     * validade data
     * @param array $data
     * @throws ValidationException
     * @return array $fields
     */
    public function validate(
        array $data
    ) {
        $rules = $this->getRules();

        $validator = $this->validator($data, $rules);
        
        if ($validator->fails()) {
            throw new ValidationException(
                $validator->messages()->toArray(),
                422
            );
        }
        
        return array_keys($rules);
    }

    /**
     * @codeCoverageIgnore
     * return new validator
     * @param array $data
     * @param array $rules
     * @return Validator
     */
    public function validator(
        array $data,
        array $rules
    ) {
        return Validator::make(
            $data,
            $rules
        );
    }
}
