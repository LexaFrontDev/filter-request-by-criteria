<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Conditions\FindByDate;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Query\QueryBuilder;

final class DateApplier implements CriteriaApplierInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function apply(QueryBuilder $qb, string $alias, FilterDto $dto, int $countWhere): int
    {
        foreach ($dto->getConditions() as $group) {
            if (!$group instanceof ConditionGroup) continue;

            foreach ($group->conditions as $i => $condition) {
                if (!$condition instanceof FindByDate) continue;

                $params = [];

                if (!empty($condition->YmdDate)) {
                    try {
                        $d = new \DateTimeImmutable($condition->YmdDate);
                        $params[] = [
                            'expr' => sprintf('%s.%s = :%s', $alias, $group->column, "{$group->column}_date_{$i}"),
                            'param' => "{$group->column}_date_{$i}",
                            'value' => $d->format('Y-m-d'),
                        ];
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }

                if (!empty($condition->YmdTime)) {
                    try {
                        $dt = new \DateTimeImmutable($condition->YmdTime);
                        $params[] = [
                            'expr' => sprintf('%s.%s = :%s', $alias, $group->column, "{$group->column}_datetime_{$i}"),
                            'param' => "{$group->column}_datetime_{$i}",
                            'value' => $dt->format('Y-m-d H:i:s'),
                        ];
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }

                if (!empty($condition->YmdTimeStamp)) {
                    try {
                        $ts = (new \DateTimeImmutable())->setTimestamp((int)$condition->YmdTimeStamp);
                        $params[] = [
                            'expr' => sprintf('%s.%s = :%s', $alias, $group->column, "{$group->column}_ts_{$i}"),
                            'param' => "{$group->column}_ts_{$i}",
                            'value' => $ts->format('Y-m-d H:i:s'),
                        ];
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }

                foreach ($params as $param) {
                    if ($countWhere === 0) {
                        $qb->where($param['expr']);
                    } else {
                        $qb->andWhere($param['expr']);
                    }
                    $qb->setParameter($param['param'], $param['value']);
                    $countWhere++;
                }
            }
        }

        return $countWhere;
    }
}