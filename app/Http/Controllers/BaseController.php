<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;

class BaseController extends Controller
{
    /**
     * clear array fields
     * @param array $data
     * @return array
     */
    public function clearArrayFields(
        array $data
    ): array {
        foreach ($data as $field => $value) {
            if (is_array($value)) {
                unset($data[$field]['*']);
            }
        }
        return $data;
    }
}
