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
        return preg_replace('/[^\\pL\d_]+/u', '/', $path);
    }
}
