# Библиотека фильтров запросов (DTO-based) пока в разработке

Эта библиотека позволяет строить **типизированные и структурированные запросы** через DTO. Поддерживаются фильтры, пагинация, связи (joins) и сортировка.

Ссылка на английскую версию: [English version](./readme.md)

---

## 1. FilterDto

`FilterDto`  основной DTO для построения запросов. Он представляет структуру запроса и обеспечивает строгую типизацию.

### Конструктор

```php
public function __construct(
    public readonly ?array $where,
    public readonly ?Pagination $pagination,
    public readonly ?array $joins = null,
    public readonly ?OrderBy $orderBy = null
)
```

* `where`: массив `ConditionGroup`  условия фильтрации
* `pagination`: `Pagination|null`  настройки пагинации
* `joins`: массив|null связи с другими таблицами
* `orderBy`: `OrderBy|null` сортировка результатов

### Статический метод фабрики

```php
public static function Filter(?array $where, ?Pagination $pagination, ?array $joins = null, ?OrderBy $orderBy = null): self
```

---

### Пример использования

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

`ConditionGroup` определяет одно условие фильтра. Поддерживаются логические операторы AND и OR.

### Конструктор

```php
public function __construct(
    public readonly string $column,
    public readonly Criterion|FindByDate $condition,
    public readonly LogicOperator $LogicOperator = LogicOperator::OR
)
```

### Статические методы

```php
ConditionGroup::and(string $column, Criterion|FindByDate $condition): self
ConditionGroup::or(string $column, Criterion|FindByDate $condition): self
```

* `and()` - строит условие с логикой AND
* `or()` - строит условие с логикой OR

---

### Пример

```php
ConditionGroup::and('name', Criterion::in(['Leha', 'Alisa']));
ConditionGroup::or('role', Criterion::eq('admin'));
```

---

## 3. Criterion

`Criterion` задаёт оператор условия фильтрации (`=`, `IN`, `LIKE` и др.).

### Примеры методов

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

Дополнительные свойства для расширенных запросов:

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