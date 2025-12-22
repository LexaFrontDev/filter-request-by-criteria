<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\LogicOperator;
use App\ReqFilter\CriteriaDto\Conditions\ComparisonOperator;
use App\ReqFilter\CriteriaDto\Conditions\Criterion;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

final class CriterionApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, FilterDto $dto, int $countWhere): int {
        foreach ($dto->where as $group) {
            if (!$group instanceof ConditionGroup) continue;
            if ($group->conditions === []) continue;


            // CASE 1: eq + OR  → IN
            if ($this->canBeIn($group)) {
                $countWhere = $this->applyIn($qb, $alias, $group, $countWhere);
                continue;
            }

            // CASE 2: (a OR b) / (a AND b)
            $countWhere = $this->applyGrouped($qb, $alias, $group, $countWhere);
        }

        return $countWhere;
    }

    private function canBeIn(ConditionGroup $group): bool
    {
        if ($group->logic !== LogicOperator::OR) return false;

        foreach ($group->conditions as $condition) {
            if (!$condition instanceof Criterion || $condition->operator !== ComparisonOperator::EQUAL) {
                return false;
            }
        }
        return true;
    }

    private function applyIn(QueryBuilder $qb, string $alias, ConditionGroup $group, int $countWhere): int
    {
        $param = $this->buildParamName($group->column, $countWhere);
        $values = array_map(fn (Criterion $c) => $c->value, $group->conditions);

        $expr = sprintf('%s.%s IN (:%s)', $alias, $group->column, $param);
        $this->addWhere($qb, $expr, $group->logic, $countWhere);
        $qb->setParameter($param, $values);

        return $countWhere + 1;
    }

    private function applyGrouped(QueryBuilder $qb, string $alias, ConditionGroup $group, int $countWhere): int
    {
        $parts = [];
        // corrected mistake with  properties when the name of properties could change the sql parameter
        foreach ($group->conditions as $i => $condition) {
            if (is_array($condition)) {
                foreach ($condition as $c) {
                    if (!$c instanceof Criterion) continue;
                    $param = $this->buildParamName($group->column, $countWhere . '_' . $i);
                    $parts[] = sprintf('%s.%s %s :%s', $alias, $group->column, $c->operator->value, $param);
                    $qb->setParameter($param, $c->value);
                }
                continue;
            }

            if (!$condition instanceof Criterion) continue;
            $param = $this->buildParamName($group->column, $countWhere . '_' . $i);
            $parts[] = sprintf('%s.%s %s :%s', $alias, $group->column, $condition->operator->value, $param);
            $qb->setParameter($param, $condition->value);
        }

        if ($parts === []) return $countWhere;
        $glue = $group->logic === LogicOperator::OR ? ' OR ' : ' AND ';
        $expr = '(' . implode($glue, $parts) . ')';
        $this->addWhere($qb, $expr, LogicOperator::AND, $countWhere);
        return $countWhere + 1;
    }

    private function addWhere(QueryBuilder $qb, string $expr, LogicOperator $logic, int $countWhere): void
    {
        if ($countWhere === 0) {
            $qb->where($expr);
            return;
        }

        $logic === LogicOperator::OR ? $qb->orWhere($expr) : $qb->andWhere($expr);
    }

    private function buildParamName(string $field, string|int $index): string
    {
        return sprintf('%s_%s', $field, $index);
    }
}

