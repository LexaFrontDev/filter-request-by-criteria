<?php

namespace App\Tests\ReqFilter\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\OrderBy;
use App\ReqFilter\Domain\Model\Common\OrderDirection;
use App\ReqFilter\Domain\Validate\Validators\OrderByValidator;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;
use PHPUnit\Framework\TestCase;

class OrderByValidatorTest extends TestCase
{
    private OrderByValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new OrderByValidator();
    }

    public function testValidateSuccess(): void
    {
        // Arrange
        $orderBy = OrderBy::by('created_at', OrderDirection::DESC);
        $dto = FilterDto::create()->setOrderBy($orderBy);

        // Act
        $result = $this->validator->validate($dto);

        // Assert
        $this->assertTrue($result);
    }

    public function testValidateSuccessWhenOrderByIsNull(): void
    {
        // Arrange
        $dto = FilterDto::create();

        // Act
        $result = $this->validator->validate($dto);

        // Assert
        $this->assertTrue($result);
    }

    public function testValidateEmptyFieldThrowsException(): void
    {
        // Arrange
        $orderBy = OrderBy::by('', OrderDirection::ASC);
        $dto = FilterDto::create()->setOrderBy($orderBy);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("OrderBy field cannot be empty");

        // Act
        $this->validator->validate($dto);
    }

    public function testValidateEmptyStringWithSpacesFieldThrowsException(): void
    {
        // Arrange
        $orderBy = OrderBy::by('   ', OrderDirection::ASC);
        $dto = FilterDto::create()->setOrderBy($orderBy);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("OrderBy field cannot be empty");

        // Act
        $this->validator->validate($dto);
    }
}
