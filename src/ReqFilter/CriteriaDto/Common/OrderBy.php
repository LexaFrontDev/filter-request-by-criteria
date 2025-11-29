<?php

namespace App\ReqFilter\CriteriaDto\Common;

final class OrderBy
{
    public function __construct(
        public readonly string $field,
        public readonly string $direction = OrderDirection::DESC,
    ) {
    }
}