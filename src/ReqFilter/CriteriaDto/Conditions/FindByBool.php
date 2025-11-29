<?php

namespace App\ReqFilter\CriteriaDto\Conditions;

final class FindByBool
{
    public function __construct(
        public bool $value
    ) {}
}