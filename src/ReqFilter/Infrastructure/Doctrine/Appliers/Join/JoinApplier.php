<?php

namespace App\ReqFilter\Infrastructure\Doctrine\Appliers\Join;

use App\ReqFilter\Domain\Model\Common\ConditionGroup;
use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\LogicOperator;
use App\ReqFilter\Domain\Model\Conditions\ComparisonOperator;
use App\ReqFilter\Domain\Model\Conditions\Criterion;
use App\ReqFilter\Domain\Model\Join\JoinType;
use App\ReqFilter\Infrastructure\Doctrine\Appliers\Contract\CriteriaApplierInterface;
use Doctrine\DBAL\Query\QueryBuilder;

class JoinApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, FilterDto $dto, int $countWhere): int
    {
        foreach ($dto->getJoins() as $join) {
            $onExpr = $this->buildOnCondition($qb, $join->getOn(), $join->getTable()->alias, $countWhere);

            match ($join->getJoinType()) {
                JoinType::LEFT->value  => $qb->leftJoin($alias, $join->getTable()->tableName, $join->getTable()->alias, $onExpr),
                JoinType::RIGHT->value => $qb->rightJoin($alias, $join->getTable()->tableName, $join->getTable()->alias, $onExpr),
                JoinType::INNER->value => $qb->innerJoin($alias, $join->getTable()->tableName, $join->getTable()->alias, $onExpr),
                default => throw new \InvalidArgumentException("Unknown join type: {$join->getJoinType()}")
            };

            foreach ((array) $join->getSelect() as $field) {
                $qb->addSelect("{$join->getTable()->alias}.{$field}");
            }
        }

        return $countWhere;
    }

    private function buildOnCondition(QueryBuilder $qb, array $groups, string $joinAlias, int &$countWhere): string
    {
        if (!$groups) return '1=1';
        $exprs = [];

        foreach ($groups as $group) {
            if (!$group instanceof ConditionGroup || empty($group->conditions)) continue;

            if ($group->logic === LogicOperator::OR && $this->allEqualOperators($group->conditions)) {
                $param = sprintf('join_%s_%d', $joinAlias, $countWhere++);
                $values = array_map(fn(Criterion $c) => $c->value, $group->conditions);
                $qb->setParameter($param, $values);
                $exprs[] = sprintf('%s.%s IN (:%s)', $joinAlias, $group->column, $param);
                continue;
            }

            $parts = [];
            foreach ($group->conditions as $i => $c) {
                if (!$c instanceof Criterion) continue;
                $param = sprintf('join_%s_%d_%d', $joinAlias, $countWhere, $i);
                $qb->setParameter($param, $c->operator === ComparisonOperator::LIKE || $c->operator === ComparisonOperator::NOT_LIKE ? "%{$c->value}%" : $c->value);
                $parts[] = sprintf('%s.%s %s :%s', $joinAlias, $group->column, $c->operator->value, $param);
            }
            $exprs[] = '(' . implode($group->logic === LogicOperator::OR ? ' OR ' : ' AND ', $parts) . ')';
            $countWhere++;
        }

        return implode(' AND ', $exprs);
    }

    private function allEqualOperators(array $conditions): bool
    {
        foreach ($conditions as $c) {
            if (!$c instanceof Criterion || $c->operator !== ComparisonOperator::EQUAL) return false;
        }
        return true;
    }
}


