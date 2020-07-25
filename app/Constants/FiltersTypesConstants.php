<?php

namespace App\Constants;

class FiltersTypesConstants
{
    const FILTER_EQUAL = 'eql';
    const FILTER_GREATER_THAN = 'gt';
    const FILTER_GREATER_THAN_OR_EQUAL = 'gte';
    const FILTER_LESS_THAN = 'lt';
    const FILTER_LESS_THAN_OR_EQUAL = 'lte';
    const FILTER_LIKE = 'lik';
    const FILTER_NOT_EQUAL = 'neq';
    const FILTER_NOT_NULL = 'nnu';
    const FILTER_NULL = 'nul';

    const ACTION_WHERE = 'where';
    const ACTION_WHERE_LIKE = 'whereLike';
    const ACTION_WHERE_NULL = 'whereNull';
    const ACTION_WHERE_NOT_NULL = 'whereNotNull';

    const FILTER_TYPE_MAP = [
        self::FILTER_EQUAL => [
            'action' => self::ACTION_WHERE,
            'signal' => '=',
        ],
        self::FILTER_GREATER_THAN => [
            'action' => self::ACTION_WHERE,
            'signal' =>'>',
        ],
        self::FILTER_GREATER_THAN_OR_EQUAL => [
            'action' => self::ACTION_WHERE,
            'signal' =>'>=',
        ],
        self::FILTER_LESS_THAN => [
            'action' => self::ACTION_WHERE,
            'signal' =>'<',
        ],
        self::FILTER_LESS_THAN_OR_EQUAL => [
            'action' => self::ACTION_WHERE,
            'signal' =>'<=',
        ],
        self::FILTER_LIKE => [
            'action' => self::ACTION_WHERE_LIKE,
            'signal' =>'like',
        ],
        self::FILTER_NOT_EQUAL => [
            'action' => self::ACTION_WHERE,
            'signal' =>'<>',
        ],
        self::FILTER_NOT_NULL => [
            'action' => self::ACTION_WHERE_NOT_NULL,
        ],
        self::FILTER_NULL => [
            'action' => self::ACTION_WHERE_NULL,
        ],
    ];
}
