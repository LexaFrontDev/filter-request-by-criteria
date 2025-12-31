# FilterDto: примеры написания базовых запросов

### Содержание

* [Что такое FilterDto](#1-что-такое-filterdto)
* [Метод addCondition](#2-метод-addcondition)
* [Метод addJoin](#3-метод-addjoin)
* [Метод setPagination](#4-метод-setpagination)
* [Метод setOrderBy](#5-метод-setorderby)
* [Базовые запросы (Criterion и FindByDate)](#6-базовые-запросы-criterion-и-findbydate)
* [Пример запроса FilterDto](#7-пример-запроса-filterdto)

---

## 1. Что такое `FilterDto`

`FilterDto`  фабричное DTO, которое описывает структуру запроса и позволяет создавать фильтры **без использования сырых массивов**.

Для создания запроса используйте **статический метод** `create()`. После этого можно вызывать методы для построения фильтра:

* [`addCondition`](#2-метод-addcondition)  добавить условие (`WHERE`)
* [`addJoin`](#3-метод-addjoin)  добавить соединение (`JOIN`)
* [`setPagination`](#4-метод-setpagination)  добавить пагинацию
* [`setOrderBy`](#5-метод-setorderby)  задать сортировку

### Пример

```php
$filter = FilterDto::create();
```

---

## 2. Метод `addCondition`

Метод `addCondition` позволяет формировать базовые `WHERE`-запросы.

### Пример 1  поиск по нескольким значениям через `IN`

```php
$filter = FilterDto::create()
    ->addCondition(
        ConditionGroup::and(
            'name',
            Criterion::in(['Leha', 'Alisa', 'Kiril'])
        )
    );
```

### Пример 2  несколько условий с оператором `OR`

```php
$filter = FilterDto::create()
    ->addCondition(
        ConditionGroup::or(
            'role',
            Criterion::eq('admin'),
            Criterion::eq('user')
        )
    );
```

> Метод `and()` связывает критерии через `AND`, метод `or()`  через `OR`.
> Базовые запросы см. [здесь](#6-базовые-запросы-criterion-и-findbydate).

---

## 3. Метод `addJoin`

Метод `addJoin` добавляет соединения (`JOIN`) к фильтру.

* `create()` принимает объект `Table` с **названием таблицы** и **алиасом**
* `select()`  строку или массив полей для выборки
* `innerJoin()`, `leftJoin()`, `rightJoin()`  тип соединения
* `on()`  объект `OnCondition`, аналогичный `Criterion`, с поддержкой `LogicOperator`

### Пример

```php
$filter = FilterDto::create()
    ->addJoin(
        Join::create(Table::is('card','cd'))
            ->select('name')
            ->innerJoin()
            ->on(OnCondition::eq('list_id', 1, LogicOperator::OR))
            ->on(OnCondition::eq('list_id', 2))
    );
```

> Несколько вызовов `on()` добавляют условия соединения.

---

## 4. Метод `setPagination`

Метод `setPagination` добавляет пагинацию к запросу.

Принимает объект `Pagination`, который можно создать с помощью метода `by()`:

```php
$filter = FilterDto::create()
    ->setPagination(Pagination::by(limit: 50, offset: 50));
```

> Пагинация удобна для больших наборов данных и помогает управлять количеством возвращаемых записей.

---

## 5. Метод `setOrderBy`

Метод `setOrderBy` задаёт сортировку. Принимает:

* **название столбца**
* **enum `OrderDirection`** (`ASC` или `DESC`)

### Пример

```php
$filter = FilterDto::create()
    ->setOrderBy(OrderBy::by('name', OrderDirection::DESC));
```

---

## 6. Базовые запросы (Criterion и FindByDate)

### Сравнения

```php
Criterion::eq('value');      // =
Criterion::notEq('value');   // !=
Criterion::gr(10);           // >
```

### IN / NOT IN

```php
Criterion::in(['Leha','Alisa']);
Criterion::notIn([1,2,3]);
```

### LIKE / NOT LIKE

```php
Criterion::like('text');
Criterion::notLike('admin');
```

### FindByDate

```php
FindByDate::by(string $YmdDate = '', string $YmdTime = '', string $YmdTimeStamp = '');
```

> В будущем `FindByDate` можно объединить с `Criterion` для удобства.
> Полный список методов доступен в API Reference.

---

## 7. Пример запроса FilterDto

```php
$filter = FilterDto::create()
    ->addCondition(
        ConditionGroup::and('name', Criterion::in(['Leha','Alisa','Kiril']))
    )
    ->addCondition(
        ConditionGroup::or(
            'role',
            Criterion::eq('admin'),
            Criterion::eq('user')
        )
    )
    ->setPagination(Pagination::by(limit: 50, offset: 50))
    ->addJoin(
        Join::create(Table::is('card','cd'))
            ->select('name')
            ->innerJoin()
            ->on(OnCondition::eq('list_id',1))
            ->on(OnCondition::eq('list_id',2))
    )
    ->setOrderBy(OrderBy::by('name', OrderDirection::DESC));
```

