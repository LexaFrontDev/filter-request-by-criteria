<?php

namespace App\ReqFilter\Domain\Model\Join;

use App\ReqFilter\Domain\Model\Common\Table;

final class Join
{
    private Table $table;

    /*** @var string[] */
    private array $select = [];
    private JoinType $joinType = JoinType::INNER;
    /** @var OnCondition[]|null */

    private ?array $on = null;

    private function __construct(Table $table)
    {
        $this->table = $table;
    }

    public static function create(Table $table): self
    {
        return new self($table);
    }

    public function select(string $columns): self
    {
        $this->select[] = $columns;
        return $this;
    }

    public function innerJoin(): self
    {
        $this->joinType = JoinType::INNER;
        return $this;
    }

    public function leftJoin(): self
    {
        $this->joinType = JoinType::LEFT;
        return $this;
    }

    public function rightJoin(): self
    {
        $this->joinType = JoinType::RIGHT;
        return $this;
    }

    public function on(OnCondition $condition): self
    {
        if ($this->on === null) {
            $this->on = [];
        }
        $this->on[] = $condition;
        return $this;
    }

    // --- Getters для DTO ---
    public function getTable(): Table
    {
        return $this->table;
    }

    public function getSelect(): array
    {
        return $this->select;
    }

    public function getJoinType(): string
    {
        return $this->joinType->value;
    }

    public function getOn(): ?array
    {
        return $this->on;
    }
}
