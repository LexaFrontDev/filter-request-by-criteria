<?php

namespace App\ReqFilter\CriteriaDto\Conditions;

final class FindByInt
{
    /**
     * @param int[] $in
     * @param int[] $notIn
     */
    public function __construct(
        public readonly ?int $value  = null,
        public readonly array $in = [],
        public readonly array $notIn = [],
    ) {
    }
}