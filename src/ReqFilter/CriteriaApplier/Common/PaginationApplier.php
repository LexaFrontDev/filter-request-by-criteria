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
        if ($dto->pagination instanceof Pagination) {
            if ($dto->pagination->paginationEnabled) {
                if (null !== $dto->pagination->offset) $qb->setFirstResult($dto->pagination->offset);
                if (null !== $dto->pagination->limit) $qb->setMaxResults($dto->pagination->limit);
            }
        }
        return $countWhere;
    }
}