<?php

namespace App\ReqFilter\CriteriaDto\Conditions;

final class FindBy
{
    /**
     * @param int|string[] $in
     * @param int|string[] $notIn
     * @param string|int|null $like
     * @param string|int|null $notLike
     * @param string|null $operator
     * @param string|int|bool|null $value
     */
    public function __construct(
        public readonly array $in = [],
        public readonly array $notIn = [],
        public readonly string|int|null $like = null,
        public readonly string|int|null $notLike = null,
        public readonly ?string $operator = '=',
        public readonly string|int|bool|null $value = null,
    ) {}
}

