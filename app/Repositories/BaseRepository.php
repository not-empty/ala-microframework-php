<?php

namespace App\Repositories;

use App\Constants\FiltersTypesConstants;
use DatabaseCache\Repository;
use Illuminate\Database\DatabaseManager;
use Ulid\Ulid;

abstract class BaseRepository
{
    protected $table;
    protected $db;
    protected $ulid;
    private $cacheRepository;

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
        $identifier = $this->table . ':' . $id;
        $cache = $this->getQuery($identifier);
        if ($cache) {
            return $cache;
        }

        $result = (array) $this->db->table($this->table)
            ->whereNull('deleted')
            ->find($id);

        if (empty($result)) {
            return $result;
        }

        $this->setQuery($identifier, $result);
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
        $identifier = $this->table . ':' . $id;
        $cache = $this->getQuery($identifier);
        if ($cache) {
            return $cache;
        }

        $result = (array) $this->db->table($this->table)
            ->whereNotNull('deleted')
            ->find($id);

        if (empty($result)) {
            return $result;
        }

        $this->setQuery($identifier, $result);
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

        $identifier = $this->table . $this->generateIdentifierByArray($query) . $page;
        $cache = $this->getQuery($identifier);
        if ($cache) {
            return $cache;
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

        $this->setQuery($identifier, $result);
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

        $identifier = $this->table . $this->generateIdentifierByArray($query) . $page;
        $cache = $this->getQuery($identifier);
        if ($cache) {
            return $cache;
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

        $this->setQuery($identifier, $result);
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
        $identifier = $this->table . ':bulk' . $this->generateIdentifierByArray($ids['*']);
        $cache = $this->getQuery($identifier);
        if ($cache) {
            return $cache;
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

        $this->setQuery($identifier, $result);
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
            $data['created'] = date('Y-m-d H:i:s');
        }
        $data['modified'] = date('Y-m-d H:i:s');

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
        $this->delQuery($identifier);

        $data = $this->arrayToJson(
            $data
        );
        $data['modified'] = date('Y-m-d H:i:s');
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
        $this->delQuery($identifier);

        $data = [];
        $data['modified'] = date('Y-m-d H:i:s');
        $data['deleted'] = date('Y-m-d H:i:s');
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
    protected function setFilters(
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
    protected function arrayToJson(
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
     * generate identifier using values in array
     * @param array $array
     * @return string
     */
    public function generateIdentifierByArray(
        array $array
    ): string {
        if (!$this->cacheRepository) {
            $this->cacheRepository = $this->newCacheRepository();
        }

        return $this->cacheRepository->generateIdentifierByArray($array);
    }

    /**
     * get database data in cache
     * @param string $identifier
     * @return string
     */
    public function getQuery(
        string $identifier
    ): ?array {
        if (!$this->cacheRepository) {
            $this->cacheRepository = $this->newCacheRepository();
        }

        $getQuery = $this->cacheRepository->getQuery($identifier);
        return json_decode($getQuery, true);
    }

    /**
     * remove database cache from redis
     * @param string $identifier
     * @return bool
     */
    public function delQuery(
        string $identifier
    ): bool {
        if (!$this->cacheRepository) {
            $this->cacheRepository = $this->newCacheRepository();
        }

        return $this->cacheRepository->delQuery($identifier);
    }

    /**
     * put database result in cache
     * @param string $identifier
     * @param array $data
     * @return bool
     */
    public function setQuery(
        string $identifier,
        array $data
    ): bool {
        if (!$this->cacheRepository) {
            $this->cacheRepository = $this->newCacheRepository();
        }

        return $this->cacheRepository->setQuery($identifier, json_encode($data));
    }

    /**
     * @codeCoverageIgnore
     * get redis config
     * @return array
     */
    public function getCacheConfig(): array
    {
        return config('database_cache');
    }

    /**
     * @codeCoverageIgnore
     * return cache repository class
     * @return array
     */
    public function newCacheRepository(): Repository
    {
        $config = $this->getCacheConfig();
        return new Repository($config);
    }
}
