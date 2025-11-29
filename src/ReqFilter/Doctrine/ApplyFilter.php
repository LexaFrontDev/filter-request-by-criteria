<?php

namespace App\ReqFilter\Doctrine;

use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaDto\Common\Table;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class ApplyFilter implements FilterInterface
{
    private QueryBuilder $qb;

    /**
     * @param iterable<CriteriaApplierInterface> $criterionAppliers
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private readonly iterable $criterionAppliers,
    ) {
    }


    /**
     * @return $this
     */
    public function initFilter(?object $criteriasDto, Table $table, string $select = '*'): self
    {
        $this->qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->select($select)
            ->from($table->tableName, $table->alias);

        if (empty((array) $criteriasDto)) {
            return $this;
        }

        $countWhere = 0;

        foreach ((array) $criteriasDto as $field => $criterion) {
            foreach ($this->criterionAppliers as $applier) {
                $countWhere = $applier->apply($this->qb, $table->alias, $field, $criterion, $countWhere);
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