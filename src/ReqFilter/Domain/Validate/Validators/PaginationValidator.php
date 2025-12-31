<?php

namespace App\ReqFilter\Domain\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Validate\Contract\DefaultValidatorInterface;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;

final class PaginationValidator implements DefaultValidatorInterface
{
    /**
     * @throws ValidatorException
     */
    public function validate(FilterDto $dto): bool
    {
        $pgDto = $dto->getPagination();
        if ($pgDto === null) return true;
        if($pgDto->limit > 1000) throw new ValidatorException("the limit is too high", [$pgDto]);
        if($pgDto->offset > 1000)  throw new ValidatorException("the offset is too high", [$pgDto]);
        return true;
    }
}