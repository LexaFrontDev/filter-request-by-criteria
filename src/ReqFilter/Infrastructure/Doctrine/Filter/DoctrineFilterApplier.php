<?php

namespace App\ReqFilter\Infrastructure\Doctrine\Filter;

use App\ReqFilter\Contracts\FilterInterface;
use App\ReqFilter\Domain\Model\Common\FilterDto;
use App\ReqFilter\Domain\Model\Common\Table;
use App\ReqFilter\Domain\Model\Common\UnionPart;
use App\ReqFilter\Domain\Validate\Contract\DefaultValidatorInterface;
use App\ReqFilter\Infrastructure\Doctrine\Appliers\Contract\CriteriaApplierInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineFilterApplier implements FilterInterface
{
    private QueryBuilder $qb;

    /**
     * @param iterable<CriteriaApplierInterface> $criterionAppliers
     * @param iterable<DefaultValidatorInterface> $criterionValidators
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private readonly iterable $criterionValidators,
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
        $this->qb = $this->entityManager->getConnection()->createQueryBuilder()->select($select)->from($table->tableName, $table->alias);
        $this->applyValidate($criterion);
        return $this->applyFilters($criterion, $table);
    }


    public function union(UnionPart $unionPart, bool $isAll = false): self
    {
        $sqlParts = [];
        $params = [];

        foreach ($unionPart->getParts() as $index => $part) {
            $select = is_array($part->getSelect()) ? implode(', ', $part->getSelect()) : $part->getSelect();
            $from = $part->getTable()->tableName . ' ' . $part->getTable()->alias;
            $sql = "SELECT $select FROM $from";
            if ($part->getFilterDto() !== null) {
                $this->applyValidate($part->getFilterDto());
                $qbTemp = $this->entityManager->getConnection()->createQueryBuilder()->select($select)->from($part->getTable()->tableName, $part->getTable()->alias);
                foreach ($this->criterionAppliers as $applier)
                    $applier->apply($qbTemp, $part->getTable()->alias, $part->getFilterDto(), 0);
                $sql = $qbTemp->getSQL();
                foreach ($qbTemp->getParameters() as $key => $value)
                    $params[$key . '_' . $index] = $value;
                foreach ($qbTemp->getParameters() as $key => $value)
                    $sql = str_replace(':' . $key, ':' . $key . '_' . $index, $sql);
            }

            $sqlParts[] = "($sql)";
        }

        $unionSql = implode($isAll ? ' UNION ALL ' : ' UNION ', $sqlParts);
        $this->qb = $this->entityManager->getConnection()->createQueryBuilder()->select('*')->from("($unionSql)", 'u');
        foreach ($params as $key => $value) $this->qb->setParameter($key, $value);
        return $this;
    }




    /**
     * @param FilterDto|null $criteriasDto
     * @param Table $table
     * @return $this
     */
    private function applyFilters(?FilterDto $criteriasDto, Table $table): self{
        if (empty($criteriasDto->getConditions()) && empty($criteriasDto->getJoins()) && empty($criteriasDto->getPagination()) && empty($criteriasDto->getOrderBy())) {
            return $this;
        }
        foreach ($this->criterionAppliers as $applier) $applier->apply($this->qb, $table->alias, $criteriasDto, 0);
        return $this;
    }


    private function applyValidate(?FilterDto $dto): void
    {
        if($dto === null) return;
        foreach ($this->criterionValidators as $validator)  $validator->validate($dto);
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