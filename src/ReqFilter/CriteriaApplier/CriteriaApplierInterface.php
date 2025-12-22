<?php

namespace App\ReqFilter\CriteriaApplier;

use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
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