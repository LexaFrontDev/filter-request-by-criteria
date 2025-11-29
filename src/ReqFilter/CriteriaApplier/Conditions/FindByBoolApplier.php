<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use App\ReqFilter\CriteriaDto\Conditions\FindByBool;
use Doctrine\DBAL\Query\QueryBuilder;

class FindByBoolApplier  implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if ($criterion instanceof FindByBool) {
            if ($criterion->value) {
                $paramName = $field . '_param_' . $countWhere;
                $qb->andWhere("$alias.$field = :$paramName")
                    ->setParameter($paramName, true);
            }
        }
        return $countWhere;
    }

}