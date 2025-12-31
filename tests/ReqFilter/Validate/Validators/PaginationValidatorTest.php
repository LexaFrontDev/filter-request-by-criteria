<?php

namespace App\Tests\ReqFilter\Validate\Validators;

use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\Pagination;
use App\ReqFilter\Domain\Validate\Validators\PaginationValidator;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;
use PHPUnit\Framework\TestCase;

class PaginationValidatorTest extends TestCase
{
    private PaginationValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PaginationValidator();
    }

    public function testValidateSuccess(): void
    {
        // Arrange
        $pagination = Pagination::By(limit: 100, offset: 0);
        $dto = FilterDto::create()->setPagination($pagination);

        // Act
        $result = $this->validator->validate($dto);

        // Assert
        $this->assertTrue($result);
    }

    public function testValidateLimitTooHighThrowsError(): void
    {
        // Arrange
        $pagination = Pagination::By(limit: 1001, offset: 0);
        $dto = FilterDto::create()->setPagination($pagination);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("the limit is too high");

        // Act
        $this->validator->validate($dto);
    }

    public function testValidateOffsetTooHighThrowsError(): void
    {
        // Arrange
        $pagination = Pagination::By(limit: 100, offset: 1001);
        $dto = FilterDto::create()->setPagination($pagination);

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("the offset is too high");

        // Act
        $this->validator->validate($dto);
    }
}
