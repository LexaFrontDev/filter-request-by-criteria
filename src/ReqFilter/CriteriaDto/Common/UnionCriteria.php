<?php

namespace App\ReqFilter\CriteriaDto\Common;

final class UnionCriteria
{
    public function __construct(
        public readonly FilterDto $filterDto,
        public readonly Table $table,
        public readonly string|array $select,
    ){}
    public static function un(Table $table, string|array $select, FilterDto $filterDto): UnionCriteria
    {
        return new self($filterDto, $table, $select);
    }
}