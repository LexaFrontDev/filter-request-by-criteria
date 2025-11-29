<?php

namespace App\ReqFilter\CriteriaDto\Join;

use App\ReqFilter\CriteriaDto\Common\Table;

final class Join
{
    /**
     * @param string[]|string $select
     * @param OnCondition[]|null $onCondition
     * @param array<string, mixed> $paramsJoin
     */
    public function __construct(
        public readonly Table $table,
        public readonly string|array $select = [],
        public readonly string $joinType = JoinType::INNER,
        public readonly ?array $onCondition = null,
        public readonly array $paramsJoin = [],
    ) {
    }
}