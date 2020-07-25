<?php

namespace App\Businesses;

abstract class BaseBusiness
{
    /**
     * decodeJsonFields
     * @param array $data
     * @param array $fields
     * @return array
     */
    public function decodeJsonFields(
        array $data,
        array $fields
    ): array {
        foreach ($fields as $field) {
            $data[$field] = json_decode($data[$field], true) ?? [];
        }
        return $data;
    }

    /**
     * @codeCoverageIgnore
     * get laravel config
     * @param string $config
     * @return array|null
     */
    public function getConfig(
        string $config
    ): ?array {
        return config($config);
    }
}
