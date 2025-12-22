<?php

namespace App\Tests\ReqFilterTest;

use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\LogicOperator;
use App\ReqFilter\CriteriaDto\Common\OrderBy;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use App\ReqFilter\CriteriaDto\Common\Table;
use App\ReqFilter\CriteriaDto\Conditions\Criterion;
use App\ReqFilter\CriteriaDto\Conditions\FindByDate;
use App\ReqFilter\CriteriaDto\Join\Join;
use App\ReqFilter\CriteriaDto\Join\JoinType;
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
        $emptyDto =  FilterDto::Filter(
           where: [
               ConditionGroup::and(column: 'name', condition: Criterion::in(['Leha', 'Alisa', 'Kiril']),),
               ConditionGroup::and(column: 'name', condition: Criterion::in(['Leha', 'Alisa', 'Kiril']),),
               ConditionGroup::or(
                    'role',
                     Criterion::eq('admin'),
                     Criterion::eq('user'),
                     Criterion::eq('manager')
               ),
               ConditionGroup::or(
                    'date',
                    FindByDate::By(YmdDate: '2025251'),
                    FindByDate::By(YmdDate: '2025261'),
                    FindByDate::By(YmdDate: '2025271'),
               ),],
           pagination: Pagination::By(50, true, true),
           joins: [
                Join::make(
                    table: Table::is('card', 'cd'),
                    select: ['name'],
                    joinType: JoinType::INNER,
                    on: [
                       OnCondition::eq('list_id', 1, LogicOperator::OR),
                       OnCondition::eq('list_id', 2, LogicOperator::OR)
                    ],
                )
          ],
           orderBy: new OrderBy('name',  'desc'),
        );

        // act
        $result = $this->Filter->initFilter(criteriasDto: $emptyDto,  table:  Table::is(tableName: 'list', alias: 'l',));

        dump($result->getSql());
        dump($result->getParameter());
        $this->assertNotNull($result->getSql());
    }

}