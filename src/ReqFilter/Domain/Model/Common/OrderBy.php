<?php

namespace App\ReqFilter\Domain\Model\Common;

final class OrderBy
{
    private function __construct(
        public readonly string $field,
        public readonly OrderDirection $direction = OrderDirection::DESC,
    ) {
    }

    public static function by(string $field, OrderDirection $direction): OrderBy
    {
        return new self($field, $direction);
    }
}