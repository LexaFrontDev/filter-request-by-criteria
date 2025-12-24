# Union Query Example

### Contents

* [Creating the General DTO](#1-creating-the-general-dto)
* [UnionCriteria  Single Query](#2-unioncriteria-single-query)
* [Examples of Building Union Queries](#3-examples-of-building-union-queries)
* [Applying Union](#4-applying-union)

---

## 1. Creating the General DTO

`UnionPart` is a general DTO for union queries. It serves as a mapper, providing:

* strong typing;
* convenient query structure building;
* elimination of manual array construction.

### Creating an Instance

```php
$union = UnionPart::create();
```

### Main Method

```php
public function setPart(UnionCriteria $part): self
```

> Each call to `setPart` adds one query to the union.

---

## 2. UnionCriteria  Single Query

`UnionCriteria` represents **a single SQL query** within the union.

### Static Mapper `un()`

```php
$part = UnionCriteria::un(
    Table::is('card','cd'),
    ['title', 'id'],
    FilterDto::create()
        ->addCondition(
            ConditionGroup::and('id', Criterion::in([1,2,3,4,5,6]))
        )
);
```

> For working with `FilterDto`, see the [FilterDto README](FilterDto.md).

---

## 3. Examples of Building Union Queries

```php
$filter = UnionPart::create()
    ->setPart(UnionCriteria::un(
        Table::is('card','cd'),
        ['title', 'id'],
        FilterDto::create()
            ->addCondition(
                ConditionGroup::and('id', Criterion::in([1,2,3,4,5,6]))
            )
    ))
    ->setPart(UnionCriteria::un(
        Table::is('user','u'),
        ['name', 'id'],
        FilterDto::create()
            ->addCondition(
                ConditionGroup::and('id', Criterion::in([1,2,3,4,5,6]))
            )
            ->setOrderBy(OrderBy::by('name','desc'))
    ));
```

---

## 4. Applying Union

The `FilterInterface` is used to apply union queries.

### Interface Method

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

### Usage Examples

Without `UNION ALL`:

```php
$result = $this->Filter->union(unionPart: $filter);
```

With `UNION ALL`:

```php
$result = $this->Filter->union(unionPart: $filter, isAll: true);
```
