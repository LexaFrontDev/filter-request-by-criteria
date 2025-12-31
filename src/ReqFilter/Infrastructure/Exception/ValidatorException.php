<?php

namespace App\ReqFilter\Infrastructure\Exception;

use App\ReqFilter\Domain\ExceptionContracts\InvalidFilterValueInterface;

class ValidatorException extends \Exception implements InvalidFilterValueInterface
{
    public function __construct(string $message, private array $context = [], int $code = 0, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
