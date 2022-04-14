<?php

namespace App\Businesses;

use App\Repositories\BaseRepository;

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
     * setRepositoryTable
     * @param BaseRepository $repository
     * @return bool
     */
    public function setRepositoryTable(
        BaseRepository $repository
    ): bool {
        $repository->setTable(
            $repository->getTable() . $this->getConfig('app')['db_suffix']
        );
        return true;
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
