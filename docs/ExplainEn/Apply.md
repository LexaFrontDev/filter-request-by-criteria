# FilterInterface  Short Documentation

### Contents

To use filters, it is recommended to first review the filters themselves: [FilterDto](FilterDto.md) and [UnionPart](union.md).

* [initFilter](#1-initfilter)
* [union](#2-union)
* [getList](#3-getlist)
* [getOne](#4-getone)
* [getSql](#5-getsql)
* [getCount](#6-getcount)
* [getParameter](#7-getparameter)

---

## 1. `initFilter`

Applies standard filters using `FilterDto`.

```php
$this->filter->initFilter(
    criterias: $filterDto,
    table: Table::is('list', 'l'),
    select: 'name'
);
```

> Returns `$this`. You can chain other methods after applying the filter.

---

## 2. `union`

Applies union queries using `UnionPart`.

```php
$this->filter->union(
    unionPart: $unionPart,
    isAll: true // UNION ALL, default is false (regular UNION)
);
```

> Also returns `$this`.

---

## 3. `getList`

Returns an array of results after applying filters or union queries.

```php
$result = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getList();

$unionResult = $this->filter
    ->union($unionPart, true)
    ->getList();
```

---

## 4. `getOne`

Returns **a single record** or `null` if no data is found.

```php
$item = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getOne();
```

---

## 5. `getSql`

Returns the SQL query as a string, useful for debugging.

```php
$sql = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getSql();
```

---

## 6. `getCount`

Returns the number of records that match the filter.

```php
$count = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getCount();
```

---

## 7. `getParameter`

Returns an array of parameters for a prepared statement.

```php
$params = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getParameter();
```

