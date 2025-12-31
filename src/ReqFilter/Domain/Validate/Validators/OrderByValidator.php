<?php

namespace App\ReqFilter\Domain\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Validate\Contract\DefaultValidatorInterface;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;

final class OrderByValidator implements DefaultValidatorInterface
{
    /**
     * @throws ValidatorException
     */
    public function validate(FilterDto $dto): bool
    {
        $orderBy = $dto->getOrderBy();
        if ($orderBy === null) return true;
        if (trim($orderBy->field) === '') throw new ValidatorException("OrderBy field cannot be empty", ['orderBy' => $orderBy]);
        return true;
    }
}