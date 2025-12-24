# DTO-based Query Filtering Library *(WIP)*

This library is designed to build **typed, extensible, and safe SQL queries** on top of Doctrine `QueryBuilder` using DTOs.

The main goal is to **eliminate unstructured arrays, manual SQL string concatenation, and implicit logic**, replacing them with a declarative and verifiable query description.

---

## Features

Supported functionality:

* Filtering (`WHERE`)
* Logical groups (`AND`, `OR`)
* `JOIN`
* Sorting (`ORDER BY`)
* Pagination
* `UNION` / `UNION ALL`

All operations are described via DTOs or Criteria objects and are validated before being applied to the query.

---

## General Concept

### Core Principles

* ❌ No unstructured input arrays

* ❌ No business logic passed via arrays

* ❌ No string-based SQL

* ❌ No “magic” keys

* ✅ DTOs and Value Objects

* ✅ Arrays are allowed **only as data** (IN, SELECT, value collections)

* ✅ Explicit contracts

* ✅ Strong typing

* ✅ Extensibility without modifying the core

### How It Works

1. A query is described using **DTOs or Criteria**
2. The DTO is validated
3. The DTO is transformed into a set of Criteria
4. The Criteria are applied to the `QueryBuilder`

As a result:

* query logic is separated from infrastructure
* the code is easy to read and extend
* errors are caught **before SQL execution**

---

## Documentation Structure

The documentation is split into independent parts.

### 1. Filtering (`WHERE`)

Description of basic filters, logical groups, and criteria.

 [FilterDto and Criteria documentation](./docs/ExplainEn/FilterDto.md)

---

### 2. `UNION` / `UNION ALL`

Description of building union queries using a shared DTO and separate Criteria.

 [UNION documentation](./docs/ExplainEn/union.md)

---

### 3. Applying DTOs to QueryBuilder

How DTOs and Criteria are applied to the `QueryBuilder`, execution order, and layer responsibilities.

 [Applying filters documentation](./docs/ExplainEn/Apply.md)

---

## When to Use This Library

This library is especially useful if:

* you have complex filters with `AND` / `OR`
* your API accepts dynamic filtering parameters
* you use `UNION` queries
* strong typing and strict query structure control are important
* the project is long-lived and maintained by multiple developers

---

## Project Status

 **Work in Progress**

* Documentation is evolving
* Breaking changes are possible

---

## Documentation Language

* 🇷🇺 Russian version  [Russian version](./readmeru.md)
* 🇬🇧 English version: current

---

If you use this library in a real project, **it is recommended to read the documentation sequentially**, starting with `FilterDto`.
