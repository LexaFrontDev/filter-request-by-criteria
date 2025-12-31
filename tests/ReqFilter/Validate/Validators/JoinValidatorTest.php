<?php

namespace App\Tests\ReqFilter\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\Table;
use App\ReqFilter\Domain\Model\Join\Join;
use App\ReqFilter\Domain\Model\Join\OnCondition;
use App\ReqFilter\Domain\Validate\Validators\JoinValidator;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;
use PHPUnit\Framework\TestCase;

class JoinValidatorTest extends TestCase
{
    private JoinValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new JoinValidator();
    }

    public function testValidateSuccess(): void
    {
        // Arrange
        $table = Table::is('users', 'u');
        $join = Join::create($table)
            ->on(OnCondition::eq('id', 1));
        
        $dto = FilterDto::create()->addJoin($join);

        // Act
        $result = $this->validator->validate($dto);

        // Assert
        $this->assertTrue($result);
    }

    public function testValidateEmptyTableNameThrowsException(): void
    {
        // Arrange
        $table = Table::is('', 'u');
        $join = Join::create($table);
        $dto = FilterDto::create()->addJoin($join);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("Table name cannot be empty");

        // Act
        $this->validator->validate($dto);
    }

    public function testValidateEmptyTableAliasThrowsException(): void
    {
        // Arrange
        $table = Table::is('users', '');
        $join = Join::create($table);
        $dto = FilterDto::create()->addJoin($join);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("Table alias cannot be empty");

        // Act
        $this->validator->validate($dto);
    }

    public function testValidateEmptyOnConditionColumnThrowsException(): void
    {
        // Arrange
        $table = Table::is('users', 'u');
        $join = Join::create($table)
            ->on(OnCondition::eq('', 1));
        $dto = FilterDto::create()->addJoin($join);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("OnCondition column cannot be empty");

        // Act
        $this->validator->validate($dto);
    }

    public function testValidateEmptyStringOnConditionValueThrowsException(): void
    {
        // Arrange
        $table = Table::is('users', 'u');
        $join = Join::create($table)
            ->on(OnCondition::eq('name', '   '));
        $dto = FilterDto::create()->addJoin($join);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("OnCondition value cannot be empty string");

        // Act
        $this->validator->validate($dto);
    }

    public function testValidateEmptyArrayOnConditionValueThrowsException(): void
    {
        // Arrange
        $table = Table::is('users', 'u');
        $join = Join::create($table)
            ->on(OnCondition::in('role', []));
        $dto = FilterDto::create()->addJoin($join);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("OnCondition array value cannot be empty");

        // Act
        $this->validator->validate($dto);
    }

    public function testValidateNullOnConditionValueThrowsException(): void
    {
        // Arrange
        $table = Table::is('users', 'u');
        $join = Join::create($table)
            ->on(OnCondition::eq('deleted_at', null));
        $dto = FilterDto::create()->addJoin($join);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("OnCondition value cannot be null");

        // Act
        $this->validator->validate($dto);
    }

    public function testValidateNegativeIntegerOnConditionValueThrowsException(): void
    {
        // Arrange
        $table = Table::is('users', 'u');
        $join = Join::create($table)
            ->on(OnCondition::eq('age', -5));
        $dto = FilterDto::create()->addJoin($join);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("OnCondition value must be positive");

        // Act
        $this->validator->validate($dto);
    }
}
