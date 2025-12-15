<?php

namespace App\ReqFilter\CriteriaDto\Common;

use App\ReqFilter\CriteriaDto\Conditions\Criterion;
use App\ReqFilter\CriteriaDto\Conditions\FindByDate;

final class ConditionGroup
{
    public function __construct(
        public readonly string                          $column,
        public readonly Criterion|FindByDate|Pagination $condition,
        public readonly string                          $LogicOperator = LogicOperator::or
    ){}

    public static function and(string $column, Criterion|FindByDate|Pagination $condition): self
    {
      return  new self(column: $column, condition: $condition, LogicOperator: LogicOperator::and);
    }


    public static function or(string $column, Criterion|FindByDate|Pagination $condition): self
    {
        return  new self(column: $column, condition: $condition, LogicOperator: LogicOperator::or);
    }
}