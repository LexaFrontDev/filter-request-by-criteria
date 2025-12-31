<?php

namespace App\ReqFilter\Domain\Model\Conditions;

final class FindByDate
{
    public function __construct(
        public readonly string $YmdDate = '',
        public readonly string $YmdTime = '',
        public readonly string $YmdTimeStamp = '',
    ) {
    }
    public static function By(string $YmdDate = '', string $YmdTime = '', string $YmdTimeStamp = ''): self
    {
        return new self($YmdDate, $YmdTime, $YmdTimeStamp);
    }
}