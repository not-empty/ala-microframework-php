<?php

namespace App\Utils;

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

        if ($path == '/' || count($arrayPath) == 2) {
            return $path;
        }

        return '/' . $arrayPath[1] . '/' . $arrayPath[2];
    }
}
