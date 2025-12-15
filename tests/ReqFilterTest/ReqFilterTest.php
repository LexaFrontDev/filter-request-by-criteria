<?php

namespace App\Tests\ReqFilterTest;

use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\LogicOperator;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use App\ReqFilter\CriteriaDto\Common\Table;
use App\ReqFilter\CriteriaDto\Conditions\Criterion;
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
               ConditionGroup::and(
                   column: 'name', condition: Criterion::in(['Leha', 'Alisa', 'Kiril']),
               ),
               ConditionGroup::or(
                    column: 'role', condition: Criterion::eq('admin'),
               ),
               ConditionGroup::or(
                    column: 'role', condition: Criterion::eq('user'),
               ),
           ],
           pagination: Pagination::By(50, true, true),
           joins: null,
           orderBy: null,
        );

        // act
        $result = $this->Filter->initFilter(criteriasDto: $emptyDto,  table:  Table::is(tableName: 'list', alias: 'l',));

        dump($result->getSql());
        dump($result->getParameter());
        $this->assertNotNull($result->getSql());
    }

}