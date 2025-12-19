<?php

namespace App\ReqFilter\CriteriaDto\Join;

use App\ReqFilter\CriteriaDto\Common\Table;

final class Join
{
    /**
     * @param string[]|string $select
     * @param OnCondition[]|null $on
     */
    public function __construct(
        public readonly Table $table,
        public readonly string|array $select = [],
        public readonly string $joinType = JoinType::INNER,
        public readonly ?array $on = null,
    ) {
    }


    /**
     * @param Table $table
     * @param string|array $select
     * @param array|null $on
     * @return Join
     */
    public static function make(Table $table, string|array $select = [], string $joinType = JoinType::INNER, ?array $on = null): Join
    {
        return new self($table, $select, $joinType, $on);
    }
}