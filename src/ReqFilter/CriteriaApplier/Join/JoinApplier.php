<?php

namespace App\ReqFilter\CriteriaApplier\Join;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Join\Join;
use App\ReqFilter\CriteriaDto\Join\OnCondition;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
class JoinApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if (!$criterion instanceof Join) {
            return $countWhere;
        }

        $onExpr = $criterion->onCondition ? $this->buildOnCondition($qb, $criterion->onCondition, $criterion->paramsJoin) : '1=1';

        $joinType = strtoupper($criterion->joinType ?? 'INNER');
        switch ($joinType) {
            case 'LEFT':
                $qb->leftJoin($alias, $criterion->table->tableName, $criterion->table->alias, $onExpr);
                break;
            case 'RIGHT':
                $qb->rightJoin($alias, $criterion->table->tableName, $criterion->table->alias, $onExpr);
                break;
            default:
                $qb->innerJoin($alias, $criterion->table->tableName, $criterion->table->alias, $onExpr);
        }

        foreach ((array) $criterion->select as $f) {
            $qb->addSelect("{$criterion->table->alias}.$f");
        }

        return $countWhere;
    }

    /**
     * @param OnCondition[] $conditions
     * @param array<string, mixed> $params
     */
    private function buildOnCondition(QueryBuilder $qb, array $conditions, array $params): CompositeExpression|string
    {
        $onExpr = null;

        foreach ($conditions as $cond) {
            if ($cond->isRawExpr()) {
                $current = $cond->expr;
            } else {
                if (null !== $cond->rightParam) {
                    $placeholder = ':'.$cond->rightParam;
                    $current = $qb->expr()->comparison($cond->left, $cond->operator ?? '=', $placeholder);

                    if (array_key_exists($cond->rightParam, $params)) {
                        $qb->setParameter($cond->rightParam, $params[$cond->rightParam]);
                    }
                } else {
                    $current = $qb->expr()->comparison($cond->left, $cond->operator ?? '=', $cond->right);
                }
            }

            if (null === $onExpr) {
                $onExpr = $current;
            } else {
                $onExpr = new CompositeExpression(
                    'OR' === strtoupper($cond->type)
                        ? CompositeExpression::TYPE_OR
                        : CompositeExpression::TYPE_AND,
                    [$onExpr, $current]
                );
            }
        }

        return $onExpr ?? '1=1';
    }
}