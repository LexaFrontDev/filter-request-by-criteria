<?php

namespace App\ReqFilter\CriteriaDto\Common;



class FilterDto {
        /**
         * @param ConditionGroup[] $where
         * @param array|null $joins
         * @param OrderBy|null $orderBy
         */
        public function __construct(
            public readonly ?array $where,
            public readonly ?Pagination $pagination,
            public readonly ?array $joins = null,
            public readonly ?OrderBy $orderBy = null
        ){}

        public static function Filter(?array $where, ?Pagination $pagination, ?array $joins = null, ?OrderBy $orderBy = null): self
        {
            return new self($where, $pagination, $joins, $orderBy);
        }
}
