<?php

namespace App\Tests\ReqFilter\ReqFilter\Apply;

use App\Tests\Fixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\Domain\Model\Common\ConditionGroup;
use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\OrderBy;
use App\ReqFilter\Domain\Model\Common\OrderDirection;
use App\ReqFilter\Domain\Model\Common\Table;
use App\ReqFilter\Domain\Model\Common\UnionCriteria;
use App\ReqFilter\Domain\Model\Common\UnionPart;
use App\ReqFilter\Domain\Model\Conditions\Criterion;
use App\ReqFilter\Domain\Model\Join\Join;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ReqFilterTest extends KernelTestCase
{
    private FilterInterface $Filter;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->Filter = self::getContainer()->get(FilterInterface::class);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->loadFixtures();
    }

    private function loadFixtures(): void
    {
        $loader = new Loader();
        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $loader->addFixture(new AppFixtures($hasher));

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testGetListReturnsData()
    {
        // Act
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::eq('User 1')));

        $this->Filter->initFilter($filter, Table::is('users', 'u'));
        $result = $this->Filter->getList();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('User 1', $result[0]['name']);
        $this->assertEquals('user1@example.com', $result[0]['email']);
    }

    public function testApplyValidateThrowsException()
    {
        // Arrange
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::eq(''))); // Empty string throws

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("Criterion value cannot be empty string");

        // Act
        $this->Filter->initFilter($filter, Table::is('users', 'u'));
    }

    public function testGetOneReturnsSingleResult()
    {
        // Act
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::eq('User 1')));

        $this->Filter->initFilter($filter, Table::is('users', 'u'));
        $result = $this->Filter->getOne();

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals('User 1', $result['name']);
        $this->assertEquals('user1@example.com', $result['email']);
    }

    public function testGetOneReturnsNullWhenNotFound()
    {
        // Act
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::eq('NonExistentUser')));

        $this->Filter->initFilter($filter, Table::is('users', 'u'));
        $result = $this->Filter->getOne();

        // Assert
        $this->assertNull($result);
    }

    public function testGetCountReturnsCorrectNumber()
    {
        // Act
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::like('User %')));

        $this->Filter->initFilter($filter, Table::is('users', 'u'));
        $count = $this->Filter->getCount();

        // Assert
        $this->assertEquals(5, $count);

        // Test with specific filter
        $filterOne = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::eq('User 1')));
        $this->Filter->initFilter($filterOne, Table::is('users', 'u'));
        $countOne = $this->Filter->getCount();
        $this->assertEquals(1, $countOne);
    }

    public function testGetSqlAndParameters()
    {
        // Act
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::eq('User 1')));

        $this->Filter->initFilter($filter, Table::is('users', 'u'));
        
        $sql = $this->Filter->getSql();
        $params = $this->Filter->getParameter();

        // Assert
        $this->assertNotEmpty($sql);
        $this->assertStringContainsString('SELECT', $sql);
        $this->assertStringContainsString('FROM users u', $sql);
        $this->assertStringContainsString('name = ', $sql);
        
        $this->assertIsArray($params);
        $this->assertNotEmpty($params);
        $this->assertContains('User 1', $params);
    }

    public function testFilterWithJoinGeneratesCorrectSql()
    {
        // Arrange
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::in('User 1')))
            ->addJoin(
                Join::create(Table::is('users', 'u2')) // Assuming self join or other table for test
                    ->select('name')
                    ->innerJoin()
                    ->on(ConditionGroup::and('id', Criterion::eq(1)))
            );

        // Act
        $this->Filter->initFilter($filter, Table::is('users', 'u'));
        $sql = $this->Filter->getSql();
        $params = $this->Filter->getParameter();

        // Assert
        $this->assertStringContainsString('INNER JOIN users', $sql);
        $this->assertStringContainsString('ON', $sql);
        $this->assertNotEmpty($params);
    }

    public function testUnionGeneratesCorrectSql()
    {
        // Arrange
        $unionPart = UnionPart::create()
            ->setPart(UnionCriteria::create(Table::is('users', 'u1'))
                ->select('name')
                ->select('id')
                ->setFilter(FilterDto::create()
                    ->addCondition(ConditionGroup::and('id', Criterion::in(1)))))
            ->setPart(
                UnionCriteria::create(Table::is('users', 'u2'))
                    ->select('name')
                    ->select('id')
                    ->setFilter(FilterDto::create()
                        ->addCondition(ConditionGroup::and('id', Criterion::in(2)))
                        ->setOrderBy(OrderBy::by('name', OrderDirection::DESC)))
            );

        // Act
        $result = $this->Filter->union($unionPart, true);
        $sql = $result->getSql();

        // Assert
        $this->assertStringContainsString('UNION ALL', $sql);
        $this->assertStringContainsString('SELECT name, id FROM users', $sql);
    }

    public function testInitFilterThrowsExceptionForAssociativeArrayInCriterion()
    {
        // Arrange
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::in(key: 'value')));

        // Assert
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('values should be an array list');

        // Act
        $this->Filter->initFilter($filter, Table::is('users', 'u'));
    }
}

