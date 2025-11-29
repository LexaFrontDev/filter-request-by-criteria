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
}