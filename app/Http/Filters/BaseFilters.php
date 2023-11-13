<?php

namespace App\Http\Filters;

use App\Exceptions\Custom\FilterException;
use Illuminate\Support\Facades\Validator;

class BaseFilters
{
    public $filter;
    private $filtersData;

    /**
     * construct
     * @param array $filtersData
     * @return void
     */
    public function __construct(
        array $filtersData
    ) {
        $this->filtersData = $filtersData;
    }

    /**
     * get and return all valid filters
     * @return array
     */
    public function getValidFilters(): array
    {
        $validFilter = [];
        $keys = array_keys($this->filter);
        foreach ($keys as $key) {
            $type = $this->isValidFilterType($key);
            $data = $this->isValidFilterData($key);
            if (!empty($type)) {
                $validFilter[$key] = [
                    'type' => $type,
                    'data' => $data,
                ];
            }
        }
        return $validFilter;
    }

    /**
     * validate type
     * @param string $key
     * @throws FilterException
     * @return string|null
     */
    private function isValidFilterType(
        string $key
    ): ?string {
        $typeOnly = null;
        if (array_key_exists($key, $this->filtersData)) {
            $typeOnly = $this->typeOnly($this->filtersData[$key]);
            $permissions = $this->filter[$key]['permissions'];
            if (!in_array($typeOnly, $permissions)) {
                throw new FilterException(
                    'Filter type ' . $typeOnly . ' not allowed to field ' . $key,
                    422
                );
            }
        }
        return $typeOnly;
    }

    /**
     * validate data
     * @param string $key
     * @throws FilterException
     * @return string|null
     */
    private function isValidFilterData(
        string $key
    ): ?string {
        $valueOnly = null;
        if (array_key_exists($key, $this->filtersData)) {
            $valueOnly = $this->valueOnly($this->filtersData[$key]);
            $validateArray = [
                $key => $valueOnly,
            ];
            $rulesArray = [
                $key => $this->filter[$key]['validate'],
            ];
            if (!empty($valueOnly)) {
                $valid = $this->validateFilter($validateArray, $rulesArray);
                if (!$valid) {
                    throw new FilterException(
                        'Invalid filter value to field ' . $key,
                        422
                    );
                }
            }
        }
        return $valueOnly;
    }

    /**
     * get only the value
     * @param string $stringData
     * @return string|null
     */
    private function valueOnly(
        string $stringData
    ) {
        $dataArray = explode(',', $stringData);
        return $dataArray[1] ?? null;
    }

    /**
     * get only the type
     * @param string $stringData
     * @return string|null
     */
    private function typeOnly(
        string $stringData
    ) {
        $dataArray = explode(',', $stringData);
        return $dataArray[0] ?? null;
    }

    /**
     * validate filter
     * @param string $stringData
     * @param string $stringData
     * @return bool
     */
    private function validateFilter(
        array $data,
        array $rule
    ): bool {
        $validator = $this->validate($data, $rule);

        if ($validator->fails()) {
            return false;
        }
        return true;
    }

    /**
     * @codeCoverageIgnore
     * create and return validator
     * @param array $data
     * @param array $rule
     * @return Validator
     */
    public function validate(
        array $data,
        array $rule
    ) {
        return Validator::make(
            $data,
            $rule
        );
    }
}
