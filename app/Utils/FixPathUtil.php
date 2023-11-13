<?php

namespace App\Utils;

use App\Exceptions\Custom\RouteNotFoundException;

class FixPathUtil
{
    /**
     * fix routes paths with parameters
     * @param string $key
     * @return string
     */
    public function fixPath(
        string $path
    ): string {
        $arrayPath = explode('/', $path);
        $domain = $arrayPath[1] ?? '';
        $action = $arrayPath[2] ?? '';

        if ($path == '/' || count($arrayPath) == 2) {
            return $path;
        }

        if (
            empty($domain) ||
            empty($action)
        ) {
            throw new RouteNotFoundException(
                'Route not found',
                404
            );
        }

        return '/' . $domain . '/' . $action;
    }
}
