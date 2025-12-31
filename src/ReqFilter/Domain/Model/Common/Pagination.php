<?php

namespace App\ReqFilter\Domain\Model\Common;

final class Pagination
{
    private function __construct(
        public readonly int $limit = 0,
        public readonly int $offset = 0,
    ) {
    }


    public static function By(int $limit = 0, int $offset = 0): self{
        return new self($limit, $offset);
    }
}