# DTO-based Query Filtering Library *(WIP)*

This library is designed to build **typed, extensible, and secure SQL queries** using DTOs.
Supported features include:

* filtering (`WHERE`)
* logical groups (`AND`, `OR`)
* pagination
* `JOIN`
* sorting (`ORDER BY`)

English version: [English version](./readme.md)

---

## General Concept

The library eliminates direct SQL construction and the use of unstructured arrays.
Queries are described **declaratively** using DTOs, after which the library:

* validates the query structure
* enforces strict typing
* builds a valid `QueryBuilder`

This approach simplifies code maintenance and reduces query-level errors.

---

## 1. Enabling Filtering

To apply filtering, a repository must depend on the `FilterInterface` contract.

### Dependency Injection Example

```php
class SomeRepository
{
    public function __construct(
        private readonly FilterInterface $filter
    ) {}
}
```

The repository does not contain SQL-building logic and interacts with filtering exclusively through the contract.

---

## 2. FilterDto — Root Query DTO

`FilterDto` is the primary object that describes the query structure.

### Constructor

```php
public function __construct(
    public readonly ?array $where,
    public readonly ?Pagination $pagination,
    public readonly ?array $joins = null,
    public readonly ?OrderBy $orderBy = null
)
```

### Field Description

| Field        | Type                     | Description           |
| ------------ | ------------------------ | --------------------- |
| `where`      | `ConditionGroup[]\|null` | Filtering conditions  |
| `pagination` | `Pagination\|null`       | Pagination parameters |
| `joins`      | `Join[]\|null`           | JOIN definitions      |
| `orderBy`    | `OrderBy\|null`          | Result ordering       |

---

### Factory Method (Recommended)

To improve code readability, a static factory method is recommended:

```php
public static function Filter(
    ?array $where,
    ?Pagination $pagination,
    ?array $joins = null,
    ?OrderBy $orderBy = null
): self
```

---

## 3. Basic Filter Example

The query applies the following conditions:

* `name IN (...)`
* the user has the role `admin` **or** `user`

```php
use App\ReqFilter\CriteriaDto\Common\FilterDto;
use App\ReqFilter\CriteriaDto\Common\ConditionGroup;
use App\ReqFilter\CriteriaDto\Conditions\Criterion;

$filter = FilterDto::Filter(
    where: [
        ConditionGroup::and(
            'name',
            Criterion::in(['Leha', 'Alisa', 'Kiril'])
        ),
        ConditionGroup::or(
            'role',
            Criterion::eq('admin'),
            Criterion::eq('user')
        ),
    ],
    pagination: null
);
```

### Notes

* `ConditionGroup::and()` creates a condition with the `AND` logical operator
* `ConditionGroup::or()` creates a condition with the `OR` logical operator
* each `ConditionGroup` contains one or more `Criterion`

---

## 4. Applying the Filter

After building the `FilterDto`, the `initFilter` method is used.

```php
$result = $this->filter->initFilter(
    criteriasDto: $filter,
    table: Table::is(
        tableName: 'list',
        alias: 'l'
    )
);
```

`Table` defines:

* the base table
* the table alias
* the context for building `JOIN` and `WHERE` clauses

---

## 5. ConditionGroup

`ConditionGroup` represents a logical group of conditions for a specific column.

### Constructor

```php
public function __construct(
    public readonly string $column,
    public readonly Criterion|FindByDate $condition,
    public readonly LogicOperator $logicOperator = LogicOperator::OR
)
```

### Static Methods

```php
ConditionGroup::and(string $column, Criterion|FindByDate ...$condition): self
ConditionGroup::or(string $column, Criterion|FindByDate ...$condition): self
```

### Example

```php
ConditionGroup::and('name', Criterion::in(['Leha', 'Alisa']));
ConditionGroup::or('role', Criterion::eq('admin'));
```

---

## 6. Criterion — Condition Operator

`Criterion` describes a single atomic filtering condition.

### Supported Operators

```php
Criterion::eq($value)            // =
Criterion::notEq($value)         // !=
Criterion::gr($value)            // >
Criterion::grEq($value)          // >=
Criterion::ls($value)            // <
Criterion::lsEq($value)          // <=
Criterion::in(array $values)     // IN
Criterion::notIn(array $values)  // NOT IN
Criterion::like($value)          // LIKE
Criterion::notLike($value)       // NOT LIKE
```

---

## 7. Pagination, Join, OrderBy

### Extended Query Example

```php
$filter = FilterDto::Filter(
    where: [
        ConditionGroup::and(
            'name',
            Criterion::in(['Leha', 'Alisa', 'Kiril'])
        ),
        ConditionGroup::or(
            'role',
            Criterion::eq('admin')
        ),
    ],
    pagination: Pagination::By(
        perPage: 50,
        withTotal: true,
        withPages: true
    ),
    joins: [
        Join::make(
            table: Table::is('card', 'cd'),
            select: ['name'],
            joinType: JoinType::INNER,
            on: [
                OnCondition::eq('list_id', 1, LogicOperator::OR),
                OnCondition::eq('list_id', 2, LogicOperator::OR),
            ]
        )
    ],
    orderBy: null
);
```


