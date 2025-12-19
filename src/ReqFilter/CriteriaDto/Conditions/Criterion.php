<?php

namespace App\ReqFilter\CriteriaDto\Conditions;

final class Criterion
{
    private function __construct(
        public readonly ComparisonOperator $operator,
        public readonly mixed $value
    ) {}

    // =, !=, >, >=, <, <=

    public static function eq(string|int|bool|null $value): self
    {
        return new self(ComparisonOperator::EQUAL, $value);
    }

    public static function notEq(string|int|bool|null $value): self
    {
        return new self(ComparisonOperator::NOT_EQUAL, $value);
    }

    public static function gr(int|float $value): self
    {
        return new self(ComparisonOperator::GREATER, $value);
    }

    public static function grEq(int|float $value): self
    {
        return new self(ComparisonOperator::GREATER_EQUAL, $value);
    }

    public static function ls(int|float $value): self
    {
        return new self(ComparisonOperator::LESS, $value);
    }

    public static function lsEq(int|float $value): self
    {
        return new self(ComparisonOperator::LESS_EQUAL, $value);
    }

    // IN / NOT IN

    /**
     * @param array<int|string> $values
     */
    public static function in(array $values): self
    {
        return new self(ComparisonOperator::IN, $values);
    }

    /**
     * @param array<int|string> $values
     */
    public static function notIn(array $values): self
    {
        return new self(ComparisonOperator::NOT_IN, $values);
    }

    // LIKE / NOT LIKE

    public static function like(string $value): self
    {
        return new self(ComparisonOperator::LIKE, $value);
    }

    public static function notLike(string $value): self
    {
        return new self(ComparisonOperator::NOT_LIKE, $value);
    }
}
