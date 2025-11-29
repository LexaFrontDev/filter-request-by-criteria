<?php

namespace App\Example\Card\Infrastructure\Doctrine\Criterias\FleshList;

use App\ReqFilter\CriteriaDto\Common\Pagination;
use App\ReqFilter\CriteriaDto\Conditions\FindByBool;
use App\ReqFilter\CriteriaDto\Conditions\FindByInt;

final class ReqFleshList
{
    public function __construct(
        public readonly FindByInt $user_id,
        public readonly FindByBool $is_deleted,
        public readonly Pagination $pagination,
    ){}
}