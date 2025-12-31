<?php

namespace App\ReqFilter\Domain\Model\Join;

use App\ReqFilter\Domain\Model\Common\LogicOperator;
use App\ReqFilter\Domain\Model\Conditions\ComparisonOperator;

final class OnCondition
{
    public function __construct(
        public readonly string $column,
        public readonly ComparisonOperator $operator,
        public readonly mixed $value,
        public readonly LogicOperator $logic = LogicOperator::AND,
    ) {}

    public static function eq(string $column, mixed $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::EQUAL, $value, $logic);
    }

    public static function gr(string $column, mixed $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::GREATER, $value, $logic);
    }


    public static function grEq(string $column, mixed $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::GREATER_EQUAL, $value, $logic);
    }

    public static function ls(string $column, mixed $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::LESS, $value, $logic);
    }

    public static function lsEq(string $column, mixed $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::LESS_EQUAL, $value, $logic);
    }

    public static function like(string $column, string $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::LIKE, $value, $logic);
    }

    public static function notLike(string $column, string $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::NOT_LIKE, $value, $logic);
    }


    public static function in(string $column, array $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::IN,  $value, $logic);
    }

    public static function notIn(string $column, array $value, LogicOperator $logic = LogicOperator::AND): self
    {
        return new self($column, ComparisonOperator::NOT_IN,  $value, $logic);
    }
}
