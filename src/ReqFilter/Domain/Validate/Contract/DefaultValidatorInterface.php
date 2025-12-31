<?php

namespace App\ReqFilter\Domain\Validate\Contract;

use App\ReqFilter\Domain\Model\Common\FilterDto;

interface DefaultValidatorInterface
{
    public function validate(FilterDto $dto): bool;
}