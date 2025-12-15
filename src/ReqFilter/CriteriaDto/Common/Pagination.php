<?php

namespace App\ReqFilter\CriteriaDto\Common;

final class Pagination
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly bool $paginationEnabled = false,
    ) {
    }


    public static function By(?int $limit = null, ?int $offset = null, bool $paginationEnabled = false): self{
        return new self($limit, $offset, $paginationEnabled);
    }
}