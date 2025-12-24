<?php

namespace App\Tests\ReqFilterTest;

use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\LogicOperator;
use App\ReqFilter\CriteriaDto\Common\OrderBy;
use App\ReqFilter\CriteriaDto\Common\OrderDirection;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use App\ReqFilter\CriteriaDto\Common\Table;
use App\ReqFilter\CriteriaDto\Common\UnionCriteria;
use App\ReqFilter\CriteriaDto\Common\UnionPart;
use App\ReqFilter\CriteriaDto\Conditions\Criterion;
use App\ReqFilter\CriteriaDto\Join\Join;
use App\ReqFilter\CriteriaDto\Join\OnCondition;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReqFilterTest extends KernelTestCase
{
    private FilterInterface $Filter;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->Filter = self::getContainer()->get(FilterInterface::class);
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
            ->setPagination(Pagination::By(limit: 50, offset:  '50'))
            ->addJoin(
                Join::create(Table::is('card','cd'))
                    ->select(['name'])
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
            ->setPart(UnionCriteria::un(
                new Table('card','cd'),
                ['title', 'id'],
                FilterDto::create()
                    ->addCondition(ConditionGroup::and('id', Criterion::in([1,2,3,4,5,6])))
            ))
            ->setPart(
                UnionCriteria::un(
                    Table::is('user','u'),
                    ['name', 'id'],
                    FilterDto::create()->addCondition(ConditionGroup::or('id', Criterion::in([1,2,3,4,5,6])))->setOrderBy(OrderBy::by('name',OrderDirection::DESC))
                )
            );

        // act
        $result = $this->Filter->union(unionPart: $filter, isAll: true);

        dump($result->getSql());
        dump($result->getParameter());
        $this->assertNotNull($result->getSql());
    }


    public function testAAAgetList()
    {

    }
}