<?php

namespace App\ReqFilter\CriteriaDto\Conditions;

final class ComparisonOperator
{
    public const EQUAL = '=';
    public const NOT_EQUAL = '<>';

    public const GREATER = '>';
    public const GREATER_EQUAL = '>=';

    public const LESS = '<';
    public const LESS_EQUAL = '<=';

    public const IN = 'IN';
    public const NOT_IN = 'NOT IN';

    public const LIKE = 'LIKE';
    public const NOT_LIKE = 'NOT LIKE';
}
