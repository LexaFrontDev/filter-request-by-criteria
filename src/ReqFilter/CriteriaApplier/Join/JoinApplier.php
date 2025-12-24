<?php

namespace App\ReqFilter\CriteriaApplier\Join;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\LogicOperator;
use App\ReqFilter\CriteriaDto\Conditions\ComparisonOperator;
use App\ReqFilter\CriteriaDto\Join\Join;
use App\ReqFilter\CriteriaDto\Join\JoinType;
use App\ReqFilter\CriteriaDto\Join\OnCondition;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
class JoinApplier implements CriteriaApplierInterface
{

    public function apply(QueryBuilder $qb, string $alias, FilterDto $dto, int $countWhere): int
    {
        foreach ($dto->getJoins() as $join)
        {
            $onExpr = $this->buildOnCondition($qb, $join->getOn(), $join->getTable()->alias);

            match ($join->getJoinType()) {
                JoinType::LEFT  => $qb->leftJoin($alias, $join->getTable()->tableName, $join->getTable()->alias, $onExpr),
                JoinType::RIGHT => $qb->rightJoin($alias, $join->getTable()->tableName, $join->getTable()->alias, $onExpr),
                default         => $qb->innerJoin($alias, $join->getTable()->tableName, $join->getTable()->alias, $onExpr),
            };

            foreach ((array) $join->getSelect() as $field) {
                $qb->addSelect("{$join->getTable()->alias}.{$field}");
            }
            $countWhere++;
        }


        return $countWhere;
    }


    /**
     * @param QueryBuilder $qb
     * @param OnCondition[] $conditions
     * @param string $joinAlias
     * @return string
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