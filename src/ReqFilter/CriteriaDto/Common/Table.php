<?php

namespace App\ReqFilter\CriteriaDto\Common;

final class Table
{

    public function __construct(
        public readonly string $tableName,
        public readonly string $alias = 'u',
    ){}


}