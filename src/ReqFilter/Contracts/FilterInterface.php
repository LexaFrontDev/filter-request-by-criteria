<?php

namespace App\ReqFilter\Contracts;

use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\Table;
use App\ReqFilter\CriteriaDto\Common\UnionPart;

interface FilterInterface
{
    /**
     * @return $this
     */
    public function initFilter(?FilterDto $criterion, Table $table, string $select = '*'): self;

    /**
     * @param UnionPart $unionPart
     * @param bool $isAll
     * @return self
     */
    public function union(UnionPart $unionPart, bool $isAll = false): self;

    /**
     * @return mixed[]
     */
    public function getList(): array;

    /**
     * @return mixed[]|null
     */
    public function getOne(): ?array;

    public function getSql(): string;

    public function getCount(): int;

    /**
     * @return mixed[]
     */
    public function getParameter(): array;
}