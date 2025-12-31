<?php

namespace App\ReqFilter\Domain\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Conditions\Criterion;
use App\ReqFilter\Domain\Model\Conditions\FindByDate;
use App\ReqFilter\Domain\Validate\Contract\DefaultValidatorInterface;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;

final class ConditionsValidator implements DefaultValidatorInterface
{

    /**
     * @throws ValidatorException
     */
    public function validate(FilterDto $dto): bool
    {
        $conditions = $dto->getConditions();
        foreach ($conditions as $condition) {
            
            if (trim($condition->column) === '') throw new ValidatorException("ConditionGroup column cannot be empty", ['condition' => $condition]);
            foreach ($condition->conditions as $criterion) {
                if ($criterion instanceof Criterion) {
                    $this->validateCriterion($criterion);
                } elseif ($criterion instanceof FindByDate) {
                    $this->validateFindByDate($criterion);
                }
            }
        }
        return true;
    }

    /**
     * @throws ValidatorException
     */
    private function validateCriterion(Criterion $criterion): void
    {
        $value = $criterion->value;
        switch (gettype($value)) {
            case 'string':
                if (trim($value) === '') throw new ValidatorException("Criterion value cannot be empty string", ['criterion' => $criterion]);
                break;
            case 'integer':
            case 'double':
                if ($value < 0) throw new ValidatorException("Criterion value must be positive", ['criterion' => $criterion]);
                break;
            case 'array':
                if (empty($value)) throw new ValidatorException("Criterion array cannot be empty", ['criterion' => $criterion]);
                break;
            case 'NULL':
                throw new ValidatorException("Criterion value cannot be null", ['criterion' => $criterion]);
            default:
                break;
        }
    }

    /**
     * @throws ValidatorException
     */
    private function validateFindByDate(FindByDate $criterion): void
    {
        if (trim($criterion->YmdDate) === '' && trim($criterion->YmdTime) === '' && trim($criterion->YmdTimeStamp) === '') {
            throw new ValidatorException("FindByDate must have at least one value set", ['criterion' => $criterion]);
        }
    }

}