<?php

namespace App\ReqFilter\CriteriaDto\Common;

final class Pagination
{
    private function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }


    public static function By(int $limit = 0, int $offset = 0): self{
        return new self($limit, $offset);
    }
}