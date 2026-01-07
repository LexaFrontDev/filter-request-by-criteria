<?php

namespace App\ReqFilter\Domain\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\ConditionGroup;
use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Conditions\Criterion;
use App\ReqFilter\Domain\Model\Join\Join;
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

        $conditions = $join->getOn();
        if ($conditions !== null) {
            foreach ($conditions as $condition) {
                if(!array_is_list($condition->conditions)) throw new ValidatorException('values should be an array list', ['conditionGroup' => $condition]);

                if ($condition instanceof ConditionGroup) {
                     $this->validateOnCondition($condition);
                }
            }
        }
    }

    /**
     * @throws ValidatorException
     */
    private function validateOnCondition(ConditionGroup $condition): void
    {
        if (trim($condition->column) === '') throw new ValidatorException("ConditionGroup column cannot be empty", ['condition' => $condition]);

        foreach ($condition->conditions as $value){
            if($value instanceof Criterion){
                switch (gettype($value->value)) {
                    case 'string':
                        if (trim($value->value) === '') throw new ValidatorException("Criterion value cannot be empty string", ['condition' => $condition]);
                        break;
                    case 'array':
                        if (empty($value->value)) throw new ValidatorException("Criterion array value cannot be empty", ['condition' => $condition]);
                        if(!array_is_list($value->value)) throw new ValidatorException('values should be an array list', ['condition' => $condition]);
                        break;
                    case 'NULL':
                        throw new ValidatorException("Criterion value cannot be null", ['condition' => $condition]);
                    case 'integer':
                    case 'double':
                        if ($value->value < 0) throw new ValidatorException("Criterion value must be positive", ['condition' => $condition]);
                        break;
                }
            }
        }
    }
}


