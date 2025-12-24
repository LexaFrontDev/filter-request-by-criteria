<?php

namespace App\ReqFilter\Doctrine;

use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\CriteriaApplier\ApplicatorInitializerInterface;
use App\ReqFilter\CriteriaApplier\CriteriaApplierInterface;
use App\ReqFilter\CriteriaApplier\CriteriaApplierJoinInterface;
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\Table;
use App\ReqFilter\CriteriaDto\Common\UnionPart;
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
     * The initialization requests method
     * @param FilterDto|null $criterion
     * @param Table $table
     * @param string $select
     * @return $this
     */
    public function initFilter(?FilterDto $criterion, Table $table, string $select = '*'): self
    {
        $this->qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->select($select)
            ->from($table->tableName, $table->alias);
        return $this->applyFilters($criterion, $table);
    }


    public function union(UnionPart $unionPart, bool $isAll = false): self
    {
        $sqlParts = [];
        $params = [];

        foreach ($unionPart->getParts() as $part) {
            $qb = $this->entityManager->getConnection()->createQueryBuilder()
                ->select(is_array($part->select) ? implode(', ', $part->select) : $part->select)
                ->from($part->table->tableName, $part->table->alias);

            foreach ($this->criterionAppliers as $applier) {
                $applier->apply($qb, $part->table->alias, $part->filterDto, 0);
            }

            $sqlParts[] = '(' . $qb->getSQL() . ')';
            $params = array_merge($params, $qb->getParameters());
        }

        $unionSql = implode($unionPart->isAll() || $isAll ? ' UNION ALL ' : ' UNION ', $sqlParts);
        $this->qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->select('*')
            ->from('(' . $unionSql . ')', 'u');
        foreach ($params as $key => $value) {
            $this->qb->setParameter($key, $value);
        }
        return $this;
    }


    /**
     * @param FilterDto|null $criteriasDto
     * @param Table $table
     * @return $this
     */
    private function applyFilters(?FilterDto $criteriasDto, Table $table){
        if (empty($criteriasDto->getConditions()) && empty($criteriasDto->getJoins()) && empty($criteriasDto->getPagination()) && empty($criteriasDto->getOrderBy())) {
            return $this;
        }
        foreach ($this->criterionAppliers as $applier) {
            $countWhere = $applier->apply($this->qb, $table->alias, $criteriasDto, 0);
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