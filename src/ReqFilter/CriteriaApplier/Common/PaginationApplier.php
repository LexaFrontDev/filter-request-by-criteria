<?php

namespace App\ReqFilter\CriteriaApplier\Common;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use Doctrine\DBAL\Query\QueryBuilder;


class PaginationApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, ConditionGroup $group, int $countWhere): int
    {
        if ($group->condition instanceof Pagination) {
            if ($group->condition->paginationEnabled) {
                if (null !== $group->condition->offset) {
                    $qb->setFirstResult($group->condition->offset);
                }
                if (null !== $group->condition->limit) {
                    $qb->setMaxResults($group->condition->limit);
                }


            }
        }

        return $countWhere;
    }
}