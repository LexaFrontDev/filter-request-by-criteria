Request Filter Library (DTO-based) — currently in development

This library provides a way to build strongly typed and structured requests using DTOs. It allows filtering, pagination, joins, and ordering in a clean and reusable way.

Link on the Russian version  [Russian version](./readmeru.md)



---

## 1. FilterDto

`FilterDto` is the main DTO for building requests. It represents the request structure and ensures type safety.

### Constructor

```php
public function __construct(
    public readonly ?array $where,
    public readonly ?Pagination $pagination,
    public readonly ?array $joins = null,
    public readonly ?OrderBy $orderBy = null
)
```

* `where`: array of `ConditionGroup`  conditions for filtering
* `pagination`: `Pagination|null`  pagination settings
* `joins`: array|null  join definitions
* `orderBy`: `OrderBy|null`  ordering of results

### Static factory method

```php
public static function Filter(?array $where, ?Pagination $pagination, ?array $joins = null, ?OrderBy $orderBy = null): self
```

---

### Example usage

```php
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Conditions\Criterion;

$filter = FilterDto::Filter(
    where: [
        ConditionGroup::and('name', Criterion::in(['Leha', 'Alisa', 'Kiril'])),
        ConditionGroup::or('role', Criterion::eq('admin')),
        ConditionGroup::or('role', Criterion::eq('user')),
    ],
    pagination: null,
    joins: null,
    orderBy: null
);
```

---

## 2. ConditionGroup

`ConditionGroup` defines a single condition in a filter. It supports logical operators AND and OR.

### Constructor

```php
public function __construct(
    public readonly string $column,
    public readonly Criterion|FindByDate $condition,
    public readonly LogicOperator $LogicOperator = LogicOperator::OR
)
```

### Static methods

```php
ConditionGroup::and(string $column, Criterion|FindByDate $condition): self
ConditionGroup::or(string $column, Criterion|FindByDate $condition): self
```

* `and()`  builds a condition with AND logic
* `or()`  builds a condition with OR logic

---

### Example

```php
ConditionGroup::and('name', Criterion::in(['Leha', 'Alisa']));
ConditionGroup::or('role', Criterion::eq('admin'));
```

---

## 3. Criterion

`Criterion` defines a condition operator for filtering (e.g., `=`, `IN`, `LIKE`).

### Example methods

```php
Criterion::eq($value)      // =
Criterion::notEq($value)   // !=
Criterion::gr($value)      // >
Criterion::grEq($value)    // >=
Criterion::ls($value)      // <
Criterion::lsEq($value)    // <=
Criterion::in([$values])   // IN
Criterion::notIn([$values])// NOT IN
Criterion::like($value)    // LIKE
Criterion::notLike($value) // NOT LIKE
```

---

## 4. Pagination, Join, OrderBy

These are optional properties for advanced queries:

```php
$filter = FilterDto::Filter(
    where: [
        ConditionGroup::and('name', Criterion::in(['Leha', 'Alisa', 'Kiril'])),
        ConditionGroup::or('role', Criterion::eq('admin')),
    ],
    pagination: Pagination::By(50, true, true),
    joins: [
        Join::make(
            table: Table::is('card', 'cd'),
            select: ['name'],
            joinType: JoinType::INNER,
            on: [
                OnCondition::eq('list_id', 1, LogicOperator::OR),
                OnCondition::eq('list_id', 2, LogicOperator::OR)
            ]
        )
    ],
    orderBy: null
);
```
