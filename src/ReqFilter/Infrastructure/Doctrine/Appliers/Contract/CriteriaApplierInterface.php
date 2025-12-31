<?php

namespace App\ReqFilter\Infrastructure\Doctrine\Appliers\Contract;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use Doctrine\DBAL\Query\QueryBuilder;

interface CriteriaApplierInterface
{
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @param FilterDto $dto
     * @param int $countWhere
     * @return int
     */
    public function apply(QueryBuilder $qb, string $alias,  FilterDto $dto, int $countWhere): int;
}