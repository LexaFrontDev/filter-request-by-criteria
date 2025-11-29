<?php

namespace App\ReqFilter\CriteriaApplier\Common;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use Doctrine\DBAL\Query\QueryBuilder;

class PaginationApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if ($criterion instanceof Pagination) {
            if ($criterion->paginationEnabled) {
                if (null !== $criterion->offset) {
                    $qb->setFirstResult($criterion->offset);
                }
                if (null !== $criterion->limit) {
                    $qb->setMaxResults($criterion->limit);
                }


            }
        }

        return $countWhere;
    }
}