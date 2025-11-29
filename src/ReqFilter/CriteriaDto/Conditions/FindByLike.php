<?php

namespace App\ReqFilter\CriteriaDto\Conditions;

final class FindByLike
{
    public function __construct(
        public readonly string $like = '',
        public readonly string $notLike = '',
    ) {
    }
}