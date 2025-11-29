<?php

namespace App\ReqFilter\CriteriaDto\Conditions;

final class FindByDate
{
    public function __construct(
        public readonly string $YmdDate = '',
        public readonly string $YmdTime = '',
        public readonly string $YmdTimeStamp = '',
    ) {
    }
}