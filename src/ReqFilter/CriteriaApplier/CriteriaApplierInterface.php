<?php

namespace App\ReqFilter\CriteriaApplier;

use Doctrine\DBAL\Query\QueryBuilder;

interface CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int;
}