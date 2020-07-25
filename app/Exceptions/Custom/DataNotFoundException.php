<?php

namespace App\Exceptions\Custom;

use Exception;

class DataNotFoundException extends Exception
{
    /**
     * constructor
     * @param string $message
     * @param integer $code
     */
    public function __construct(
        $message = 'Data not found',
        $code = 404
    ) {
        parent::__construct(
            $message,
            $code
        );
    }
}
