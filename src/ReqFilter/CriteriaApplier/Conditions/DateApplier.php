<?php

namespace App\ReqFilter\CriteriaApplier\Conditions;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Conditions\FindByDate;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Query\QueryBuilder;

class DateApplier implements CriteriaApplierInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function apply(QueryBuilder $qb, string $alias, string $field, object $criterion, int $countWhere): int
    {
        if (!($criterion instanceof FindByDate)) {
            return $countWhere;
        }

        $conditions = [];

        if (!empty($criterion->YmdDate)) {
            try {
                $date = new \DateTimeImmutable($criterion->YmdDate);
                $conditions[] = [
                    'expr' => "$alias.$field = :{$field}_date",
                    'param' => "{$field}_date",
                    'value' => $date->format('Y-m-d'),
                ];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        if (!empty($criterion->YmdTime)) {
            try {
                $dateTime = new \DateTimeImmutable($criterion->YmdTime);
                $conditions[] = [
                    'expr' => "$alias.$field = :{$field}_datetime",
                    'param' => "{$field}_datetime",
                    'value' => $dateTime->format('Y-m-d H:i:s'),
                ];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        if (!empty($criterion->YmdTimeStamp)) {
            try {
                $dateFromTs = (new \DateTimeImmutable())->setTimestamp((int) $criterion->YmdTimeStamp);
                $conditions[] = [
                    'expr' => "$alias.$field = :{$field}_ts",
                    'param' => "{$field}_ts",
                    'value' => $dateFromTs->format('Y-m-d H:i:s'),
                ];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        foreach ($conditions as $cond) {
            if (0 === $countWhere) {
                $qb->where($cond['expr']);
            } else {
                $qb->andWhere($cond['expr']);
            }
            $qb->setParameter($cond['param'], $cond['value']);
            ++$countWhere;
        }

        return $countWhere;
    }


}