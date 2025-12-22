# DTO-based библиотека фильтрации запросов *(WIP)*

Данная библиотека предназначена для построения **типизированных, расширяемых и безопасных SQL-запросов** с использованием DTO.
Поддерживаемый функционал:

* фильтрация (`WHERE`)
* логические группы (`AND`, `OR`)
* пагинация
* `JOIN`
* сортировка (`ORDER BY`)

Английская версия: [English version](./readme.md)

---

## Общая концепция

Библиотека исключает прямое формирование SQL-запросов и использование неструктурированных массивов.
Запрос описывается **декларативно** с помощью DTO, после чего библиотека:

* валидирует структуру запроса
* обеспечивает строгую типизацию
* формирует корректный `QueryBuilder`

Данный подход упрощает сопровождение кода и снижает количество ошибок на уровне запросов.

---

## 1. Подключение фильтрации

Для применения фильтрации репозиторий должен зависеть от контракта `FilterInterface`.

### Пример внедрения зависимости

```php
class SomeRepository
{
    public function __construct(
        private readonly FilterInterface $filter
    ) {}
}
```

Репозиторий не содержит логики построения SQL-запросов и работает исключительно через контракт.

---

## 2. FilterDto — корневой DTO запроса

`FilterDto` является основным объектом, описывающим структуру запроса.

### Конструктор

```php
public function __construct(
    public readonly ?array $where,
    public readonly ?Pagination $pagination,
    public readonly ?array $joins = null,
    public readonly ?OrderBy $orderBy = null
)
```

### Описание полей

| Поле         | Тип                      | Описание              |
| ------------ | ------------------------ | --------------------- |
| `where`      | `ConditionGroup[]\|null` | Условия фильтрации    |
| `pagination` | `Pagination\|null`       | Параметры пагинации   |
| `joins`      | `Join[]\|null`           | Описания JOIN         |
| `orderBy`    | `OrderBy\|null`          | Сортировка результата |

---

### Фабричный метод (рекомендуется)

Для повышения читаемости кода рекомендуется использовать статический фабричный метод:

```php
public static function Filter(
    ?array $where,
    ?Pagination $pagination,
    ?array $joins = null,
    ?OrderBy $orderBy = null
): self
```

---

## 3. Пример базового фильтра

Запрос выполняет следующие условия:

* `name IN (...)`
* пользователь имеет роль `admin` **или** `user`

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

### Примечания

* `ConditionGroup::and()` формирует условие с логическим оператором `AND`
* `ConditionGroup::or()` формирует условие с логическим оператором `OR`
* каждый `ConditionGroup` содержит один или несколько `Criterion`

---

## 4. Применение фильтра

После формирования `FilterDto` используется метод `initFilter`.

```php
$result = $this->filter->initFilter(
    criteriasDto: $filter,
    table: Table::is(
        tableName: 'list',
        alias: 'l'
    )
);
```

`Table` определяет:

* основную таблицу
* алиас таблицы
* контекст для формирования `JOIN` и `WHERE`

---

## 5. ConditionGroup

`ConditionGroup` представляет логическую группу условий для конкретного столбца.

### Конструктор

```php
public function __construct(
    public readonly string $column,
    public readonly Criterion|FindByDate $condition,
    public readonly LogicOperator $logicOperator = LogicOperator::OR
)
```

### Статические методы

```php
ConditionGroup::and(string $column, Criterion|FindByDate ...$condition): self
ConditionGroup::or(string $column, Criterion|FindByDate ...$condition): self
```

### Пример

```php
ConditionGroup::and('name', Criterion::in(['Leha', 'Alisa']));
ConditionGroup::or('role', Criterion::eq('admin'));
```

---

## 6. Criterion — оператор условия

`Criterion` описывает одно атомарное условие фильтрации.

### Поддерживаемые операторы

```php
Criterion::eq($value)           // =
Criterion::notEq($value)        // !=
Criterion::gr($value)           // >
Criterion::grEq($value)         // >=
Criterion::ls($value)           // <
Criterion::lsEq($value)         // <=
Criterion::in(array $values)    // IN
Criterion::notIn(array $values) // NOT IN
Criterion::like($value)         // LIKE
Criterion::notLike($value)      // NOT LIKE
```

---

## 7. Pagination, Join, OrderBy

### Пример расширенного запроса

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

