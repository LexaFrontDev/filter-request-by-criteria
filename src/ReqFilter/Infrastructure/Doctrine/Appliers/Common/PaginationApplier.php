<?php

namespace App\ReqFilter\Infrastructure\Doctrine\Appliers\Common;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\Pagination;
use App\ReqFilter\Infrastructure\Doctrine\Appliers\Contract\CriteriaApplierInterface;
use Doctrine\DBAL\Query\QueryBuilder;


class PaginationApplier implements CriteriaApplierInterface
{
    public function apply(QueryBuilder $qb, string $alias,  FilterDto $dto, int $countWhere): int
    {
        if ($dto->getPagination() instanceof Pagination) {
            if (null !== $dto->getPagination()->offset)
                $qb->setFirstResult($dto->getPagination()->offset);
            if (null !== $dto->getPagination()->limit)
                $qb->setMaxResults($dto->getPagination()->limit);
        }
        return $countWhere;
    }
}