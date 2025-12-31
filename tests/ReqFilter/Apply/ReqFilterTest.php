<?php

namespace App\Tests\ReqFilter\Apply;

use App\Example\User\Domain\Entity\User;
use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\Domain\Model\Common\ConditionGroup;
use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\LogicOperator;
use App\ReqFilter\Domain\Model\Common\OrderBy;
use App\ReqFilter\Domain\Model\Common\OrderDirection;
use App\ReqFilter\Domain\Model\Common\Pagination;
use App\ReqFilter\Domain\Model\Common\Table;
use App\ReqFilter\Domain\Model\Common\UnionCriteria;
use App\ReqFilter\Domain\Model\Common\UnionPart;
use App\ReqFilter\Domain\Model\Conditions\Criterion;
use App\ReqFilter\Domain\Model\Join\Join;
use App\ReqFilter\Domain\Model\Join\OnCondition;
use App\ReqFilter\Infrastructure\Exception\ValidatorException;
use App\Tests\Fixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
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

    public function testAAAInit()
    {
        // assert
        $filter = FilterDto::create()
            ->addCondition(ConditionGroup::and('name', Criterion::in(['Leha','Alisa','Kiril']), Criterion::like('devil')))
            ->addCondition(ConditionGroup::or(
                'role',
                Criterion::eq('admin'),
                Criterion::eq('user')
            ))
            ->setPagination(Pagination::By(limit: 50, offset:  50))
            ->addJoin(
                Join::create(Table::is('card','cd'))
                    ->select('name')
                    ->innerJoin()
                    ->on(OnCondition::eq('list_id',1, LogicOperator::AND))
                    ->on(OnCondition::eq('list_id',2))
            )
            ->setOrderBy(OrderBy::by('name',OrderDirection::DESC));


        // act
        $result = $this->Filter->initFilter(criterion: $filter,  table:  Table::is(tableName: 'list', alias: 'l'));

        dump($result->getSql());
        dump($result->getParameter());
        $this->assertNotNull($result->getSql());
    }

    public function testAAAunion()
    {
        // assert
        $filter = UnionPart::create()
            ->setPart(UnionCriteria::create(Table::is('card','cd'))
                ->select('title')
                ->select('id')
                ->setFilter(FilterDto::create()
                    ->addCondition(ConditionGroup::and('id', Criterion::in([1,2,3,4,5,6])))))
            ->setPart(
                UnionCriteria::create(Table::is('user','u'))
                    ->select('name')
                    ->select('id')
                    ->setFilter(FilterDto::create()
                        ->addCondition(ConditionGroup::or('id', Criterion::in([1,2,3,4,5,6])))
                        ->setOrderBy(OrderBy::by('name',OrderDirection::DESC)))
            );

        // act
        $result = $this->Filter->union(unionPart: $filter, isAll: true);

        dump($result->getSql());
        dump($result->getParameter());
        $this->assertNotNull($result->getSql());
    }
}