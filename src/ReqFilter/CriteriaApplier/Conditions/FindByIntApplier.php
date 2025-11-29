<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Conditions\FindByInt;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class FindByIntApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if ($criterion instanceof FindByInt) {
            // === int ===
            if (null !== $criterion->value) {
                $expr = "$alias.$field = :$field";
                $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);
                $qb->setParameter($field, $criterion->value);
                ++$countWhere;
            }

            // === in ===
            if (!empty($criterion->in)) {
                $paramName = "in_$field";
                $expr = "$alias.$field IN (:$paramName)";
                $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);

                $qb->setParameter($paramName, $criterion->in, ParameterType::INTEGER);
                ++$countWhere;
            }

            // === not in ===
            if (!empty($criterion->notIn)) {
                $paramName = "notin_$field";
                $expr = "$alias.$field NOT IN (:$paramName)";
                $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);
                $qb->setParameter($paramName, $criterion->notIn, ParameterType::INTEGER);
                ++$countWhere;
            }
        }

        return $countWhere;
    }
}