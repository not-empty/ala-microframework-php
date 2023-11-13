<?php

namespace App\Domains\Health\Http\Controllers;

use App\Http\Controllers\BaseController;

class HealthApiController extends BaseController
{
    /**
     * process the request
     * @return JsonObject
     */
    public function process()
    {
        return response()->json(
            [
                'status' => 'online',
                'version' => config('version.info'),
            ],
            200,
            [],
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE
        );
    }
}
