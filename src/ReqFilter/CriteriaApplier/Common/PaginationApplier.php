<?php

namespace App\ReqFilter\CriteriaApplier\Common;

use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use Doctrine\DBAL\Query\QueryBuilder;


class PaginationApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias,  FilterDto $dto, int $countWhere): int
    {
        if ($dto->getPagination() instanceof Pagination) {
            if (null !== $dto->getPagination()->offset) $qb->setFirstResult($dto->getPagination()->offset);
            if (null !== $dto->getPagination()->limit) $qb->setMaxResults($dto->getPagination()->limit);
        }
        return $countWhere;
    }
}