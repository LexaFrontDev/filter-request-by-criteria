<?php

namespace App\ReqFilter\Contracts;

use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\Table;

interface FilterInterface
{
    /**
     * @return $this
     */
    public function initFilter(?FilterDto $criteriasDto, Table $table, string $select = '*'): self;

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