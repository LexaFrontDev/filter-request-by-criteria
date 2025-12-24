<?php

namespace App\ReqFilter\CriteriaDto\Common;

use App\ReqFilter\CriteriaDto\Conditions\Criterion;
use App\ReqFilter\CriteriaDto\Conditions\FindByDate;
use App\ReqFilter\CriteriaDto\Join\Join;

/**
 * FilterDto represents a structured and strongly-typed filter request.
 *
 * It exists to describe the request structure explicitly and to eliminate
 * the usage of raw arrays in favor of clean, predictable and type-safe objects.
 *
 * This DTO contains no business logic and only defines the request shape.
 */
final class FilterDto
{
    /** @var ConditionGroup[] */
    private array $conditions = [];

    private ?Pagination $pagination = null;

    /** @var Join[] */
    private array $joins = [];

    private ?OrderBy $orderBy = null;

    private function __construct() {}
    public static function create(): self
    {
        return new self();
    }


    /**
     * @param ConditionGroup $condition
     * @return $this
     */
    public function addCondition(ConditionGroup $condition): self
    {
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * @param Pagination $pagination
     * @return $this
     */
    public function setPagination(Pagination $pagination): self
    {
        $this->pagination = $pagination;
        return $this;
    }

    public function addJoin(Join $join): self
    {
        $this->joins[] = $join;
        return $this;
    }

    public function setOrderBy(OrderBy $orderBy): self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }

    public function getJoins(): array
    {
        return $this->joins;
    }

    public function getOrderBy(): ?OrderBy
    {
        return $this->orderBy;
    }
}
