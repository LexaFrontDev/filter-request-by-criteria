<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\LogicOperator;
use App\ReqFilter\CriteriaDto\Conditions\ComparisonOperator;
use App\ReqFilter\CriteriaDto\Conditions\Criterion;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

final class CriterionApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, ConditionGroup $group, int $countWhere): int {
        if (!$group->condition instanceof Criterion) {
            return $countWhere;
        }

        $criterion = $group->condition;
        $paramName = $this->buildParamName($field, $countWhere);

        $expr = match ($criterion->operator) {
            ComparisonOperator::IN,
            ComparisonOperator::NOT_IN => sprintf('%s.%s %s (:%s)', $alias, $field, $criterion->operator->value, $paramName),
            ComparisonOperator::LIKE,
            ComparisonOperator::NOT_LIKE => sprintf('%s.%s %s :%s', $alias, $field, $criterion->operator->value, $paramName),
            default => sprintf('%s.%s %s :%s', $alias, $field, $criterion->operator->value, $paramName),
        };

        $this->addWhere($qb, $expr, $group->LogicOperator->value, $countWhere);
        $this->bindValue($qb, $paramName, $criterion);
        return $countWhere + 1;
    }

    private function addWhere(QueryBuilder $qb, string $expr, string $logic, int $countWhere): void {
        if ($countWhere === 0) {
            $qb->where($expr);
            return;
        }
        $logic === LogicOperator::OR ? $qb->orWhere($expr) : $qb->andWhere($expr);
    }

    private function bindValue(QueryBuilder $qb, string $paramName, Criterion $criterion): void {
        if (in_array($criterion->operator, [ComparisonOperator::IN, ComparisonOperator::NOT_IN], true)) {
            $type = is_int($criterion->value[0] ?? null) ? ParameterType::INTEGER : ParameterType::STRING;
            $qb->setParameter($paramName, $criterion->value, $type);
            return;
        }

        if (in_array($criterion->operator, [ComparisonOperator::LIKE, ComparisonOperator::NOT_LIKE], true)) {
            $qb->setParameter($paramName, '%' . $criterion->value . '%');
            return;
        }

        $qb->setParameter($paramName, $criterion->value);
    }

    private function buildParamName(string $field, int $index): string
    {
        return sprintf('%s_%d', $field, $index);
    }
}
