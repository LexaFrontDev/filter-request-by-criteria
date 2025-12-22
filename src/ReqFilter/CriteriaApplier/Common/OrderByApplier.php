<?php

namespace App\ReqFilter\CriteriaApplier\Common;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\OrderBy;
use Doctrine\DBAL\Query\QueryBuilder;

class OrderByApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias,  FilterDto $dto, int $countWhere): int
    {
        if ($dto->orderBy instanceof OrderBy) {
            $qb->addOrderBy($dto->orderBy->field, $dto->orderBy->direction);
        }

        return $countWhere;
    }
}