<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Conditions\FindByLike;
use Doctrine\DBAL\Query\QueryBuilder;

class FindByLikeApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if ($criterion instanceof FindByLike) {
            if (!empty($criterion->like)) {
                $this->addLike($qb, $alias, $field, $criterion->like, $countWhere);
                ++$countWhere;
            }
            if (!empty($criterion->notLike)) {
                $this->addNotLike($qb, $alias, $field, $criterion->notLike, $countWhere);
                ++$countWhere;
            }
        }

        return $countWhere;
    }

    private function addLike(QueryBuilder $qb, string $alias, string $field, string $value, int $countWhere): void
    {
        $condition = $qb->expr()->like("$alias.$field", ":$field");
        $countWhere > 0 ? $qb->andWhere($condition) : $qb->where($condition);
        $qb->setParameter($field, "%$value%");
    }

    private function addNotLike(QueryBuilder $qb, string $alias, string $field, string $value, int $countWhere): void
    {
        $condition = $qb->expr()->notLike("$alias.$field", ":not_$field");
        $countWhere > 0 ? $qb->andWhere($condition) : $qb->where($condition);
        $qb->setParameter("not_$field", "%$value%");
    }
}