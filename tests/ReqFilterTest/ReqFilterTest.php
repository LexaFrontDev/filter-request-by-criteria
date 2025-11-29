<?php

namespace App\Tests\ReqFilterTest;

use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\CriteriaDto\Common\Pagination;
use App\ReqFilter\CriteriaDto\Common\Table;
use App\ReqFilter\CriteriaDto\Conditions\FindBy;
use App\ReqFilter\CriteriaDto\Conditions\FindByBool;
use App\ReqFilter\CriteriaDto\Conditions\FindByInt;
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
        $emptyDto = (object)[
            'user_id' => new FindBy(
                operator: '=',
                value: 1
            ),
            'is_deleted' => new FindBy(
                operator: '=',
                value: true
            ),
            'title' => [
                new FindBy(
                    like: 'hello',
                ),
            ],
            'pagination' => new Pagination(
                limit: 50,
                offset: true,
                paginationEnabled: true
            ),
            'cardJoin' => new Join(
                table: new Table(
                    tableName: 'card',
                    alias: 'cd',
                ),
                select: 'name',
                joinType: JoinType::INNER,
                onCondition: [new OnCondition(left: 'cd.list_id',  operator: '=', rightParam: 'idss')],
                paramsJoin: ['idss' => 1],
            ),
        ];

        // act
        $result = $this->Filter->initFilter(criteriasDto: $emptyDto,  table: new Table(
            tableName: 'list',
            alias: 'l',
        ));

        dump($result->getSql());
        dump($result->getParameter());
        var_dump($result->getSql());
        $this->assertNotNull($result->getSql());
    }

}