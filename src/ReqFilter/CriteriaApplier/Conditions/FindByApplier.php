<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Conditions\FindBy;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class FindByApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if (!($criterion instanceof FindBy)) {
            return $countWhere;
        }

        // ===  operator ===
        if (null !== $criterion->operator) {
            $op = $criterion->operator;
            $expr = "$alias.$field $op :$field";
            $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);
            $qb->setParameter($field, $criterion->value);
            ++$countWhere;
        }


        // === in ===
        if (!empty($criterion->in)) {
            $paramName = "in_$field";
            $expr = "$alias.$field IN (:$paramName)";
            $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);
            $type = is_int($criterion->in[0] ?? null) ? ParameterType::INTEGER : ParameterType::STRING;
            $qb->setParameter($paramName, $criterion->in, $type);
            ++$countWhere;
        }

        // === not in ===
        if (!empty($criterion->notIn)) {
            $paramName = "notin_$field";
            $expr = "$alias.$field NOT IN (:$paramName)";
            $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);
            $type = is_int($criterion->notIn[0] ?? null) ? ParameterType::INTEGER : ParameterType::STRING;
            $qb->setParameter($paramName, $criterion->notIn, $type);
            ++$countWhere;
        }

        // === like ===
        if (null !== $criterion->like) {
            $paramName = "like_$field";
            $expr = "$alias.$field LIKE :$paramName";
            $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);
            $qb->setParameter($paramName, "%{$criterion->like}%");
            ++$countWhere;
        }

        // === not like ===
        if (null !== $criterion->notLike) {
            $paramName = "notlike_$field";
            $expr = "$alias.$field NOT LIKE :$paramName";
            $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);
            $qb->setParameter($paramName, "%{$criterion->notLike}%");
            ++$countWhere;
        }

        return $countWhere;
    }
}
