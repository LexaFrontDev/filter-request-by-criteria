<?php

namespace App\Example\Card\Infrastructure\Doctrine\Criterias\FleshList;

use App\ReqFilter\Domain\Model\Common\Pagination;
use App\ReqFilter\Domain\Model\Conditions\Criterion;

final class ReqFleshList
{
    public function __construct(
        public readonly Criterion  $user_id,
        public readonly Criterion  $is_deleted,
        public readonly Pagination $pagination,
    ){}
}