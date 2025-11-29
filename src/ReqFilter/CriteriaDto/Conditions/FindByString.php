<?php

namespace App\ReqFilter\CriteriaDto\Conditions;

final class FindByString
{
    /**
     * @param string[] $anyOf
     * @param string[] $in
     */
    public function __construct(
        public readonly string $equal = '',
        public readonly array $anyOf = [],
        public readonly array $in = [],
    ) {
    }
}