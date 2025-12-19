<?php

namespace App\ReqFilter\Doctrine;

use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaApplier\CriteriaApplierJoinInterface;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\Table;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class ApplyFilter implements FilterInterface
{
    private QueryBuilder $qb;

    /**
     * @param iterable<CriteriaApplierInterface> $criterionAppliers
     * @param iterable<CriteriaApplierJoinInterface> $criterionAppliersJoin
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private readonly iterable $criterionAppliers,
        private readonly iterable $criterionAppliersJoin,
    ) {
    }


    /**
     * TODO review this method and change array on object
     * @return $this
     */
    public function initFilter(?FilterDto $criteriasDto, Table $table, string $select = '*'): self
    {
        $this->qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->select($select)
            ->from($table->tableName, $table->alias);
        return $this->applyFilters($criteriasDto, $table, $select);
    }


    /**
     * @param FilterDto|null $criteriasDto
     * @param Table $table
     * @return $this
     * TODO added filter by join and corrected cycle
     */
    private function applyFilters(?FilterDto $criteriasDto, Table $table){
        if (empty($criteriasDto->where) && empty($criteriasDto->joins) && empty($criteriasDto->pagination) && empty($criteriasDto->orderBy)) {
            return $this;
        }

        $countWhere = 0;

        foreach ($criteriasDto->where as $criterion) {
            foreach ($this->criterionAppliers as $applier) {
                if (!$applier instanceof CriteriaApplierInterface) {
                    continue;
                }

                $countWhere = $applier->apply($this->qb, $table->alias, $criterion->column, $criterion, $countWhere);
            }
        }

        foreach ($criteriasDto->joins as $criterion) {
            foreach ($this->criterionAppliersJoin as $applier) {
                if (!$applier instanceof CriteriaApplierJoinInterface) {
                    continue;
                }

                $countWhere = $applier->apply($this->qb, $table->alias,  $criterion, $countWhere);
            }
        }


        return $this;
    }

    /**
     * @return mixed[]
     *
     * @throws Exception
     */
    public function getList(): array
    {
        return $this->qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return mixed[]|null
     *
     * @throws Exception
     */
    public function getOne(): ?array
    {
        return $this->qb->executeQuery()->fetchAssociative() ?: null;
    }

    public function getSql(): string
    {
        return $this->qb->getSQL();
    }

    /**
     * @throws Exception
     */
    public function getCount(): int
    {
        $rows = $this->qb->executeQuery()->fetchAllAssociative();

        return \count($rows);
    }

    public function getParameter(): array
    {
        return $this->qb->getParameters();
    }
}