<?php

namespace App\Tests\ReqFilter\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\ConditionGroup;
use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Conditions\Criterion;
use App\ReqFilter\Domain\Model\Conditions\FindByDate;
use App\ReqFilter\Domain\Validate\Validators\ConditionsValidator;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;
use PHPUnit\Framework\TestCase;

class ConditionsValidatorTest extends TestCase
{
    private ConditionsValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ConditionsValidator();
    }

    /**
     * @throws ValidatorException
     */
    public function testValidateStringSuccess(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('name', Criterion::eq('valid value')));
        $this->assertTrue($this->validator->validate($dto));
    }

    public function testValidateStringEmptyThrows(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('name', Criterion::eq('   ')));
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Criterion value cannot be empty string');
        $this->validator->validate($dto);
    }

    public function testValidateIntegerSuccess(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('age', Criterion::eq(10)));
        $this->assertTrue($this->validator->validate($dto));
    }

    public function testValidateIntegerNegativeThrows(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('age', Criterion::eq(-1)));
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Criterion value must be positive');
        $this->validator->validate($dto);
    }

    public function testValidateDoubleSuccess(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('price', Criterion::gr(10.5)));
        $this->assertTrue($this->validator->validate($dto));
    }

    public function testValidateDoubleNegativeThrows(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('price', Criterion::ls(-0.5)));
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Criterion value must be positive');
        $this->validator->validate($dto);
    }

    public function testValidateArraySuccess(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('ids', Criterion::in([1, 2, 3])));
        $this->assertTrue($this->validator->validate($dto));
    }

    public function testValidateArrayEmptyThrows(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('ids', Criterion::in([])));
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Criterion array cannot be empty');
        $this->validator->validate($dto);
    }

    public function testValidateNullThrows(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('deleted_at', Criterion::eq(null)));
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Criterion value cannot be null');
        $this->validator->validate($dto);
    }

    public function testValidateConditionGroupColumnEmptyThrows(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('', Criterion::eq('value')));
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('ConditionGroup column cannot be empty');
        $this->validator->validate($dto);
    }

    public function testValidateFindByDateSuccess(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('date', FindByDate::By('2023-01-01')));
        $this->assertTrue($this->validator->validate($dto));
    }

    public function testValidateFindByDateEmptyThrows(): void
    {
        $dto = FilterDto::create()->addCondition(ConditionGroup::and('date', FindByDate::By('', '', '')));
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('FindByDate must have at least one value set');
        $this->validator->validate($dto);
    }
}
