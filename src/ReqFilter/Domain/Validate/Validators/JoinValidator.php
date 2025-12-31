<?php

namespace App\ReqFilter\Domain\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Join\Join;
use App\ReqFilter\Domain\Model\Join\OnCondition;
use App\ReqFilter\Domain\Validate\Contract\DefaultValidatorInterface;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;

final class JoinValidator implements DefaultValidatorInterface
{
    /**
     * @throws ValidatorException
     */
    public function validate(FilterDto $dto): bool
    {
        $joins = $dto->getJoins();
        foreach ($joins as $join) $this->validateJoin($join);
        return true;
    }

    /**
     * @throws ValidatorException
     */
    private function validateJoin(Join $join): void
    {
        $table = $join->getTable();
        
        if (trim($table->tableName) === '') throw new ValidatorException("Table name cannot be empty", ['join' => $join]);
        if (trim($table->alias) === '') throw new ValidatorException("Table alias cannot be empty", ['join' => $join]);

        $onConditions = $join->getOn();
        if ($onConditions !== null) {
            foreach ($onConditions as $condition) {
                if ($condition instanceof OnCondition) {
                     $this->validateOnCondition($condition);
                }
            }
        }
    }

    /**
     * @throws ValidatorException
     */
    private function validateOnCondition(OnCondition $condition): void
    {
        if (trim($condition->column) === '') throw new ValidatorException("OnCondition column cannot be empty", ['condition' => $condition]);
        $value = $condition->value;
        switch (gettype($value)) {
            case 'string':
                if (trim($value) === '') throw new ValidatorException("OnCondition value cannot be empty string", ['condition' => $condition]);
                break;
            case 'array':
                if (empty($value)) throw new ValidatorException("OnCondition array value cannot be empty", ['condition' => $condition]);
                break;
            case 'NULL':
                throw new ValidatorException("OnCondition value cannot be null", ['condition' => $condition]);
            case 'integer':
            case 'double':
                if ($value < 0) throw new ValidatorException("OnCondition value must be positive", ['condition' => $condition]);
                break;
        }
    }
}
