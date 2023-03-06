<?php

namespace App\Repositories;

use App\Constants\FiltersTypesConstants;
use Illuminate\Database\DatabaseManager;
use Ulid\Ulid;

abstract class BaseRepository
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
        return (array) $this->db->table($this->table)
            ->whereNull('deleted')
            ->find($id);
    }

    /**
     * get dead data by id
     * @param string $id
     * @return array
     */
    public function getDeadById(
        string $id
    ): array {
        return (array) $this->db->table($this->table)
            ->whereNotNull('deleted')
            ->find($id);
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

        return $list->toArray();
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

        return $list->toArray();
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
        $list = $this->db->table($this->table)
            ->select($fields)
            ->whereNull('deleted')
            ->whereIn('id', $ids);

        $list = $list->orderBy($order, $class)
            ->paginate(25);

        $list->appends($query)
            ->links();

        return $list->toArray();
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
                case FiltersTypesConstants::ACTION_WHERE_IN:
                    $list->whereIn($key, explode('|', $filter['data']));
                    break;
                case FiltersTypesConstants::ACTION_WHERE_BETWEEN:
                    $this->applyWhereBetweenFilter(
                        $list,
                        $key,
                        $filter['data']
                    );
                    break;
            }
        }

        return $list;
    }

    /**
     * set whereBetweenFilter
     * @param DatabaseManager $list
     * @param string|integer $key
     * @param string $data
     * @return DatabaseManager $list
     */
    protected function applyWhereBetweenFilter(
        DatabaseManager $list,
        $key,
        String $data
    ) {
        $itens = explode('|', $data);
        if (count($itens) === 2) {
            $list->whereBetween($key, $itens);
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
     * @codeCoverageIgnore
     * get table
     * @return string
     */
    public function getTable(): string
    {
        return (string) $this->table;
    }

    /**
     * @codeCoverageIgnore
     * set table
     * @return bool
     */
    public function setTable(
        string $table
    ): bool {
        $this->table = $table;
        return true;
    }
}
