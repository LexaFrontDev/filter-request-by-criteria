<?php

namespace App\ReqFilter\CriteriaApplier;

use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Join\Join;
use Doctrine\DBAL\Query\QueryBuilder;

interface CriteriaApplierJoinInterface
{

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @param string $field
     * @param Join $criterion
     * @param int $countWhere
     * @return int
     */
    public function apply(QueryBuilder $qb, string $alias, string $field, Join $criterion, int $countWhere): int;



}