# Пример Union запроса

### Содержание

* [Создание общего DTO](#1-создание-общего-dto)
* [UnionCriteria  отдельный запрос](#2-unioncriteria-отдельный-запрос)
* [Примеры построения union-запросов](#3-примеры-построения-union-запросов)
* [Применение Union](#4-применение-union)

---

## 1. Создание общего DTO

`UnionPart`  это общий DTO для union-запросов. Он выполняет роль маппера, обеспечивая:

* строгую типизацию;
* удобство построения структуры запроса;
* избавление от ручного заполнения массивов.

### Создание экземпляра

```php
$union = UnionPart::create();
```

### Основной метод

```php
public function setPart(UnionCriteria $part): self
```

> Каждый вызов `setPart` добавляет один запрос в union.

---

## 2. UnionCriteria  отдельный запрос

`UnionCriteria` представляет **один SQL-запрос** внутри union.

### Статический маппер `un()`

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

> Для работы с `FilterDto` см. [FilterDto README](FilterDto.md).

---

## 3. Примеры построения union-запросов

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

## 4. Применение Union

Для применения union-запросов используется интерфейс `FilterInterface`.

### Метод интерфейса

```php
/**
 * @param UnionPart $unionPart
 * @param bool $isAll
 * @return self
 */
public function union(UnionPart $unionPart, bool $isAll = false): self;
```

* `$unionPart`  DTO с описанием union-запроса
* `$isAll`  тип union: `UNION ALL` или `UNION` (по умолчанию)

### Примеры использования

Без `UNION ALL`:

```php
$result = $this->Filter->union(unionPart: $filter);
```

С `UNION ALL`:

```php
$result = $this->Filter->union(unionPart: $filter, isAll: true);
```

