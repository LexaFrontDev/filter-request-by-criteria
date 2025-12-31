# FilterDto: Basic Query Examples

### Contents

* [What is FilterDto](#1-what-is-filterdto)
* [Method addCondition](#2-method-addcondition)
* [Method addJoin](#3-method-addjoin)
* [Method setPagination](#4-method-setpagination)
* [Method setOrderBy](#5-method-setorderby)
* [Basic Queries (Criterion and FindByDate)](#6-basic-queries-criterion-and-findbydate)
* [Example FilterDto Query](#7-example-filterdto-query)

---

## 1. What is `FilterDto`

`FilterDto` is a factory DTO that defines the query structure and allows creating filters **without using raw arrays**.

To create a filter, use the **static method** `create()`. Then you can call methods to build your filter:

* [`addCondition`](#2-method-addcondition)  add a `WHERE` condition
* [`addJoin`](#3-method-addjoin)  add a `JOIN`
* [`setPagination`](#4-method-setpagination)  add pagination
* [`setOrderBy`](#5-method-setorderby)  set ordering

### Example

```php
$filter = FilterDto::create();
```

---

## 2. Method `addCondition`

`addCondition` allows building basic `WHERE` queries.

### Example 1  Search for multiple values using `IN`

```php
$filter = FilterDto::create()
    ->addCondition(
        ConditionGroup::and(
            'name',
            Criterion::in(['Leha', 'Alisa', 'Kiril'])
        )
    );
```

### Example 2  Multiple conditions with `OR`

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

> `and()` connects criteria with `AND`, `or()` connects with `OR`.
> See basic queries [here](#6-basic-queries-criterion-and-findbydate).

---

## 3. Method `addJoin`

`addJoin` adds `JOIN`s to your filter.

* `create()` accepts a `Table` object with **table name** and **alias**
* `select()`  a string or array of fields to select
* `innerJoin()`, `leftJoin()`, `rightJoin()`  join type
* `on()`  `OnCondition` object, similar to `Criterion` with support for `LogicOperator`

### Example

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

> Multiple `on()` calls add multiple join conditions.

---

## 4. Method `setPagination`

`setPagination` adds pagination to your query.

It accepts a `Pagination` object, which can be created using the `by()` method:

```php
$filter = FilterDto::create()
    ->setPagination(Pagination::by(limit: 50, offset: 50));
```

> Pagination is useful for large datasets and helps control the number of returned records.

---

## 5. Method `setOrderBy`

`setOrderBy` sets ordering for your query. Accepts:

* **column name**
* **enum `OrderDirection`** (`ASC` or `DESC`)

### Example

```php
$filter = FilterDto::create()
    ->setOrderBy(OrderBy::by('name', OrderDirection::DESC));
```

---

## 6. Basic Queries (Criterion and FindByDate)

### Comparisons

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

> In the future, `FindByDate` can be merged with `Criterion` for convenience.
> Full list of methods is available in the API Reference.

---

## 7. Example FilterDto Query

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
