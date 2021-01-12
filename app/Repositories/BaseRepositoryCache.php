<?php

namespace App\Repositories;

use App\Constants\FiltersTypesConstants;
use DatabaseCache\Repository;
use Illuminate\Database\DatabaseManager;
use Ulid\Ulid;

abstract class BaseRepositoryCache
{
    protected $table;
    protected $db;
    protected $ulid;

    /**
     * constructor
     * @param DatabaseManager $db
     * @param Ulid $ulid
     * @return void
     */
    public function __construct(
        DatabaseManager $db,
        Ulid $ulid
    ) {
        $this->db = $db;
        $this->ulid = $ulid;
    }

    /**
     * get data by Id
     * @param string $id
     * @return array
     */
    public function getById(
        string $id
    ): array {
        $cacheRepository = $this->newCacheRepository();

        $identifier = $this->table . ':' . $id;
        $cache = $cacheRepository->getQuery($identifier);
        if ($cache) {
            return json_decode($cache, true);
        }

        $result = (array) $this->db->table($this->table)
            ->whereNull('deleted')
            ->find($id);

        if (empty($result)) {
            return $result;
        }

        $cacheRepository->setQuery($identifier, json_encode($result));
        return $result;
    }

    /**
     * get dead data by id
     * @param string $id
     * @return array
     */
    public function getDeadById(
        string $id
    ): array {
        $cacheRepository = $this->newCacheRepository();

        $identifier = $this->table . ':' . $id;
        $cache = $cacheRepository->getQuery($identifier);
        if ($cache) {
            return json_decode($cache, true);
        }

        $result = (array) $this->db->table($this->table)
            ->whereNotNull('deleted')
            ->find($id);

        if (empty($result)) {
            return $result;
        }

        $cacheRepository->setQuery($identifier, json_encode($result));
        return $result;
    }

    /**
     * get data list
     * @param array $fields
     * @param string $order
     * @param string $class
     * @param array|null $filters
     * @param array $query
     * @return array
     */
    public function getList(
        array $fields,
        string $order,
        string $class,
        ?array $filters,
        array $query
    ): array {
        $page = 1;
        if (isset($query['page'])) {
            $page = $query['page'];
        }

        $cacheRepository = $this->newCacheRepository();

        $identifier = $this->table . $cacheRepository->generateIdentifierByArray($query) . $page;
        $cache = $cacheRepository->getQuery($identifier);
        if ($cache) {
            return json_decode($cache, true);
        }

        $list = $this->db->table($this->table)
            ->select($fields);

        $list = $this->setWheres(
            $list,
            [
                'whereNull' => 'deleted'
            ]
        );

        $list = $this->setFilters($list, $filters);

        $list = $list->orderBy($order, $class)
            ->paginate(25, ['*'], 'page', $page);

        $list->appends($query)
            ->links();

        $result = $list->toArray();

        if (empty($result['data'])) {
            return $result;
        }

        $cacheRepository->setQuery($identifier, json_encode($result));
        return $result;
    }

    /**
     * set where condition in query
     * @param $list
     * @param array $where
     */
    public function setWheres($list, array $where)
    {
        foreach ($where as $key => $value) {
            $list->$key($value);
        }

        return $list;
    }

    /**
     * get dead data list
     * @param array $fields
     * @param string $order
     * @param string $class
     * @param array|null $filters
     * @param array $query
     * @return array
     */
    public function getDeadList(
        array $fields,
        string $order,
        string $class,
        ?array $filters,
        array $query
    ): array {
        $page = 1;
        if (isset($query['page'])) {
            $page = $query['page'];
        }

        $cacheRepository = $this->newCacheRepository();

        $identifier = $this->table . $cacheRepository->generateIdentifierByArray($query) . $page;
        $cache = $cacheRepository->getQuery($identifier);
        if ($cache) {
            return json_decode($cache, true);
        }

        $list = $this->db->table($this->table)
            ->select($fields);

        $list = $this->setWheres(
            $list,
            [
                'whereNotNull' => 'deleted'
            ]
        );

        $list = $this->setFilters($list, $filters);

        $list = $list->orderBy($order, $class)
            ->paginate(25, ['*'], 'page', $page);

        $list->appends($query)
            ->links();

        $result = $list->toArray();

        if (empty($result['data'])) {
            return $result;
        }

        $cacheRepository->setQuery($identifier, json_encode($result));
        return $result;
    }

    /**
     * get bulk list
     * @param string $id
     * @param array $fields
     * @param string $order
     * @param string $class
     * @param array $query
     * @return array
     */
    public function getBulk(
        array $ids,
        array $fields,
        string $order,
        string $class,
        array $query
    ): array {
        $cacheRepository = $this->newCacheRepository();

        $identifier = $this->table . ':bulk' . $cacheRepository->generateIdentifierByArray($ids['*']);
        $cache = $cacheRepository->getQuery($identifier);
        if ($cache) {
            return json_decode($cache, true);
        }

        $list = $this->db->table($this->table)
            ->select($fields)
            ->whereNull('deleted')
            ->whereIn('id', $ids);

        $list = $list->orderBy($order, $class)
            ->paginate(25);

        $list->appends($query)
            ->links();

        $result = $list->toArray();

        if (empty($result['data'])) {
            return $result;
        }

        $cacheRepository->setQuery($identifier, json_encode($result));
        return $result;
    }

    /**
     * insert data
     * @param array $data
     * @return string
     */
    public function insert(
        array $data,
        string $id = null
    ): string {
        $data = $this->arrayToJson(
            $data
        );
        if (!$id) {
            $id = $this->ulid->generate();
        }

        $data['id'] = $id;
        if (!isset($data['created']) || empty($data['created'])) {
            $data['created'] = $this->returnNow();
        }
        $data['modified'] = $this->returnNow();

        $this->db->table($this->table)
            ->insert($data);
        return $id;
    }

    /**
     * update data
     * @param array $data
     * @param string $id
     * @return bool
     */
    public function update(
        array $data,
        string $id
    ): bool {
        $identifier = $this->table . ':' . $id;
        $this->newCacheRepository()->delQuery($identifier);

        $data = $this->arrayToJson($data);
        $data['modified'] = $this->returnNow();

        $this->db->table($this->table)
            ->where('id', $id)
            ->whereNull('deleted')
            ->update($data);

        return true;
    }

    /**
     * delete data
     * @param string $id
     * @return bool
     */
    public function delete(
        string $id
    ): bool {
        $identifier = $this->table . ':' . $id;
        $this->newCacheRepository()->delQuery($identifier);

        $data = [];
        $data['modified'] = $this->returnNow();
        $data['deleted'] = $this->returnNow();

        $this->db->table($this->table)
            ->where('id', $id)
            ->whereNull('deleted')
            ->update($data);

        return true;
    }

    /**
     * set filters before list
     * @param DatabaseManager $list
     * @param array|null $filters
     * @return DatabaseManager
     */
    public function setFilters(
        $list,
        ?array $filters
    ) {
        if (empty($filters)) {
            return $list;
        }
        foreach ($filters as $key => $filter) {
            $map = FiltersTypesConstants::FILTER_TYPE_MAP[$filter['type']];
            switch ($map['action']) {
                case FiltersTypesConstants::ACTION_WHERE:
                    $list->where($key, $map['signal'], $filter['data']);
                    break;
                case FiltersTypesConstants::ACTION_WHERE_LIKE:
                    $data = '%' . $filter['data'] . '%';
                    $list->where($key, $map['signal'], $data);
                    break;
                case FiltersTypesConstants::ACTION_WHERE_NULL:
                    $list->whereNull($key);
                    break;
                case FiltersTypesConstants::ACTION_WHERE_NOT_NULL:
                    $list->whereNotNull($key);
                    break;
            }
        }
        return $list;
    }

    /**
     * convert array fields to json
     * @param array $data
     * @return array
     */
    public function arrayToJson(
        array $data
    ): array {
        foreach ($data as $field => $value) {
            if (is_array($value)) {
                $data[$field] = json_encode($value);
            }
        }
        return $data;
    }

    /**
     * begin transaction
     * @return bool
     */
    public function beginTrans(): bool
    {
        $this->db->beginTransaction();
        return true;
    }

    /**
     * roll back transaction
     * @return bool
     */
    public function rollBack(): bool
    {
        $this->db->rollBack();
        return true;
    }

    /**
     * commit transaction
     * @return bool
     */
    public function commit(): bool
    {
        $this->db->commit();
        return true;
    }

    /**
     * @codeCoverageIgnore
     * return now date
     * @return string
     */
    public function returnNow(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @codeCoverageIgnore
     * return cache repository class
     * @return array
     */
    public function newCacheRepository(): Repository
    {
        $cacheConfig = $this->getCacheConfig();
        return new Repository($cacheConfig);
    }
}
