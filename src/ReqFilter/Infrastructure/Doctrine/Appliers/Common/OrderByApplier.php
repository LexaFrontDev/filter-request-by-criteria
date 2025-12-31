<?php

namespace App\ReqFilter\Infrastructure\Doctrine\Appliers\Common;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\OrderBy;
use App\ReqFilter\Infrastructure\Doctrine\Appliers\Contract\CriteriaApplierInterface;
use Doctrine\DBAL\Query\QueryBuilder;

class OrderByApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias,  FilterDto $dto, int $countWhere): int
    {
        if ($dto->getOrderBy() instanceof OrderBy)
            $qb->addOrderBy($dto->getOrderBy()->field, $dto->getOrderBy()->direction->value);
        return $countWhere;
    }
}