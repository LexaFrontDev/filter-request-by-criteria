<?php

namespace App\ReqFilter\Domain\Model\Common;

final class UnionCriteria
{
    private  FilterDto $filterDto;
    private  Table $table;
    private  array $select;

    private function __construct(){}

    public static function create(Table $table): self
    {
        $obj = new self();
        $obj->table = $table;
        return $obj;
    }


    public function setFilter(FilterDto $filterDto): self
    {
        $this->filterDto = $filterDto;
        return $this;
    }


    public function select(string $select): self
    {
        $this->select[] = $select;
        return $this;
    }

    public function getSelect(): array
    {
        return $this->select;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getFilterDto(): FilterDto
    {
        return $this->filterDto;
    }
}