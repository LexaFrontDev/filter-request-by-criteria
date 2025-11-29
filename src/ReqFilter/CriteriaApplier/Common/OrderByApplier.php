<?php

namespace App\ReqFilter\CriteriaApplier\Common;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\OrderBy;
use Doctrine\DBAL\Query\QueryBuilder;

class OrderByApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if ($criterion instanceof OrderBy) {
            $qb->addOrderBy($criterion->field, $criterion->direction);
        }

        return $countWhere;
    }
}