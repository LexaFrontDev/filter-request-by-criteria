<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Conditions\FindByString;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class FindByStringApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if ($criterion instanceof FindByString) {
            if (!empty($criterion->equal)) {
                $expr = "$alias.$field = :$field";
                $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);

                $qb->setParameter($field, $criterion->equal);
                ++$countWhere;
            }


            if (!empty($criterion->anyOf)) {
                $expr = $qb->expr();
                $orX = null;

                foreach ($criterion->anyOf as $i => $val) {
                    $paramName = "{$field}_any_$i";
                    $qb->setParameter($paramName, $val);

                    $condition = "$alias.$field = :$paramName";
                    $orX = $orX ? $expr->orX($orX, $condition) : $condition;
                }

                $countWhere > 0 ? $qb->andWhere($orX) : $qb->where($orX);
                ++$countWhere;
            }


            if (!empty($criterion->in)) {
                $paramName = "in_$field";
                $expr = "$alias.$field IN (:$paramName)";
                $countWhere > 0 ? $qb->andWhere($expr) : $qb->where($expr);

                $qb->setParameter($paramName, $criterion->in, ParameterType::STRING);
                ++$countWhere;
            }
        }

        return $countWhere;
    }
}