# FilterInterface  краткая документация

### Содержание

Чтобы использовать фильтры, рекомендуется сначала изучить сами фильтры: [FilterDto](FilterDto.md) и [UnionPart](union.md).

* [initFilter](#1-initfilter)
* [union](#2-union)
* [getList](#3-getlist)
* [getOne](#4-getone)
* [getSql](#5-getsql)
* [getCount](#6-getcount)
* [getParameter](#7-getparameter)

---

## 1. `initFilter`

Применяет обычные фильтры через `FilterDto`.

```php
$this->filter->initFilter(
    criterias: $filterDto,
    table: Table::is('list', 'l'),
    select: 'name'
);
```

> Возвращает `$this`. Используйте цепочку для вызова других методов после применения фильтра.

---

## 2. `union`

Применяет union-запросы через `UnionPart`.

```php
$this->filter->union(
    unionPart: $unionPart,
    isAll: true // UNION ALL, по умолчанию false (обычный UNION)
);
```

> Также возвращает `$this`.

---

## 3. `getList`

Возвращает массив результатов после применения фильтров или union.

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

Возвращает **одну запись** или `null`, если данных нет.

```php
$item = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getOne();
```

---

## 5. `getSql`

Возвращает SQL-запрос как строку, удобно для отладки.

```php
$sql = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getSql();
```

---

## 6. `getCount`

Возвращает количество записей, подходящих под фильтр.

```php
$count = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getCount();
```

---

## 7. `getParameter`

Возвращает массив параметров для подготовленного запроса.

```php
$params = $this->filter
    ->initFilter($filterDto, Table::is('list', 'l'))
    ->getParameter();
```
