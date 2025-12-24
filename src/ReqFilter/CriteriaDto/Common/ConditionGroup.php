<?php

namespace App\ReqFilter\CriteriaDto\Common;

use App\ReqFilter\CriteriaDto\Conditions\Criterion;
use App\ReqFilter\CriteriaDto\Conditions\FindByDate;

final class ConditionGroup
{
    /**
     * @param list<Criterion|FindByDate> $conditions
     */
    private function __construct(
        public readonly string $column,
        public readonly array $conditions,
        public readonly LogicOperator $logic = LogicOperator::OR
    ) {}

    public static function and(string $column, Criterion|FindByDate ...$conditions): self
    {
        return new self($column, $conditions, LogicOperator::AND);
    }

    public static function or(string $column, Criterion|FindByDate ...$conditions): self
    {
        return new self($column, $conditions, LogicOperator::OR);
    }
}

