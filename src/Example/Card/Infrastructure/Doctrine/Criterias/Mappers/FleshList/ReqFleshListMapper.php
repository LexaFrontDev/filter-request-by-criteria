<?php

namespace App\Example\Card\Infrastructure\Doctrine\Criterias\Mappers\FleshList;

use App\Example\Card\Infrastructure\Doctrine\Criterias\FleshList\ReqFleshList;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use App\ReqFilter\CriteriaDto\Conditions\Criterion;


final class ReqFleshListMapper
{
    public function toDto($UserId): ReqFleshList
    {
        return new ReqFleshList(
            user_id: $UserId,
            is_deleted: new Criterion(
                operator: '=',
                value: false,
            ),
            pagination: new Pagination(
                limit: 10,
                offset: 0,
                paginationEnabled: false
            ),
        );
    }
}