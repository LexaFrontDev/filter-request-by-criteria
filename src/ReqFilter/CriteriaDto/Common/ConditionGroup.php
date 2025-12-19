<?php

namespace App\ReqFilter\CriteriaDto\Common;

use App\ReqFilter\CriteriaDto\Conditions\Criterion;
use App\ReqFilter\CriteriaDto\Conditions\FindByDate;

final class ConditionGroup
{
    public function __construct(
        public readonly string  $column,
        public readonly Criterion|FindByDate $condition,
        public readonly LogicOperator $LogicOperator = LogicOperator::OR
    ){}

    public static function and(string $column, Criterion|FindByDate $condition): self
    {
      return  new self(column: $column, condition: $condition, LogicOperator: LogicOperator::AND);
    }


    public static function or(string $column, Criterion|FindByDate $condition): self
    {
        return  new self(column: $column, condition: $condition, LogicOperator: LogicOperator::OR);
    }
}