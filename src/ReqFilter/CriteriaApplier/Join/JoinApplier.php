<?php

namespace App\ReqFilter\CriteriaApplier\Join;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaApplier\CriteriaApplierJoinInterface;
use App\ReqFilter\CriteriaDto\Common\LogicOperator;
use App\ReqFilter\CriteriaDto\Conditions\ComparisonOperator;
use App\ReqFilter\CriteriaDto\Join\Join;
use App\ReqFilter\CriteriaDto\Join\JoinType;
use App\ReqFilter\CriteriaDto\Join\OnCondition;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
class JoinApplier implements CriteriaApplierJoinInterface
{

    public function apply(QueryBuilder $qb, string $alias, Join $criterion, int $countWhere): int
    {
        $onExpr = $this->buildOnCondition($qb, $criterion->on, $criterion->table->alias);

        match ($criterion->joinType) {
            JoinType::LEFT  => $qb->leftJoin($alias, $criterion->table->tableName, $criterion->table->alias, $onExpr),
            JoinType::RIGHT => $qb->rightJoin($alias, $criterion->table->tableName, $criterion->table->alias, $onExpr),
            default         => $qb->innerJoin($alias, $criterion->table->tableName, $criterion->table->alias, $onExpr),
        };

        foreach ((array) $criterion->select as $field) {
            $qb->addSelect("{$criterion->table->alias}.{$field}");
        }

        return $countWhere;
    }


    /**
     * @param OnCondition[] $conditions
     */
    private function buildOnCondition(QueryBuilder $qb, array $conditions, string $joinAlias): string {
        if ($conditions === []) return '1=1';
        $expr = null;
        $i = 0;
        foreach ($conditions as $condition) {
            $param = sprintf('join_%s_%d', $joinAlias, $i++);
            $current = match ($condition->operator) {
                ComparisonOperator::IN,
                ComparisonOperator::NOT_IN => sprintf('%s.%s %s (:%s)', $joinAlias, $condition->column, $condition->operator->value, $param),
                default => sprintf('%s.%s %s :%s', $joinAlias, $condition->column, $condition->operator->value, $param),
            };

            // bind
            $qb->setParameter($param, $condition->operator === ComparisonOperator::LIKE || $condition->operator === ComparisonOperator::NOT_LIKE ? '%' . $condition->value . '%' : $condition->value);

            if ($expr === null) {
                $expr = $current;
                continue;
            }

            $expr = $condition->logic === LogicOperator::OR ? "($expr OR $current)" : "($expr AND $current)";
        }

        return $expr;
    }

}