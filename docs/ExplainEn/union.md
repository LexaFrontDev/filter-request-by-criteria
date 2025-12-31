# Union Query Example

### Table of Contents

* [Creating the main DTO](#1-creating-the-main-dto)
* [UnionCriteria  a single query](#2-unioncriteria-a-single-query)
* [Examples of building union queries](#3-examples-of-building-union-queries)
* [Using Union](#4-using-union)

---

## 1. Creating the main DTO

`UnionPart` is the main DTO for union queries. It acts as a mapper, providing:

* strict typing;
* easy construction of the query structure;
* elimination of manual array handling.

### Creating an instance

```php
$union = UnionPart::create();
```

### Main method

```php
public function setPart(UnionCriteria $part): self
```

> Each call to `setPart` adds one query to the union.

---

## 2. UnionCriteria a single query

`UnionCriteria` represents **a single SQL query** within the union.

### Static `create()` method and configuration

A fluent interface is used to set up selection and filtering.

```php
$part = UnionCriteria::create(Table::is('card','cd'))
    ->select('title')
    ->select('id')
    ->setFilter(
        FilterDto::create()
            ->addCondition(
                ConditionGroup::and('id', Criterion::in([1,2,3,4,5,6]))
            )
    );
```

> For working with `FilterDto`, see [FilterDto README](FilterDto.md).

---

## 3. Examples of building union queries

```php
$filter = UnionPart::create()
    ->setPart(
        UnionCriteria::create(Table::is('card','cd'))
            ->select('title')
            ->select('id')
            ->setFilter(
                FilterDto::create()
                    ->addCondition(
                        ConditionGroup::and('id', Criterion::in([1,2,3,4,5,6]))
                    )
            )
    )
    ->setPart(
        UnionCriteria::create(Table::is('user','u'))
            ->select('name')
            ->select('id')
            ->setFilter(
                FilterDto::create()
                    ->addCondition(
                        ConditionGroup::and('id', Criterion::in([1,2,3,4,5,6]))
                    )
                    ->setOrderBy(OrderBy::by('name', OrderDirection::DESC))
            )
    );
```

---

## 4. Using Union

The `FilterInterface` is used to execute union queries.

### Interface method

```php
/**
 * @param UnionPart $unionPart
 * @param bool $isAll
 * @return self
 */
public function union(UnionPart $unionPart, bool $isAll = false): self;
```

* `$unionPart`  DTO describing the union query
* `$isAll`  type of union: `UNION ALL` or `UNION` (default)

### Usage examples

Without `UNION ALL`:

```php
$result = $this->Filter->union(unionPart: $filter);
```

With `UNION ALL`:

```php
$result = $this->Filter->union(unionPart: $filter, isAll: true);
```
