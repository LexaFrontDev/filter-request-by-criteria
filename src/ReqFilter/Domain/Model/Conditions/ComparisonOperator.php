<?php

namespace App\ReqFilter\Domain\Model\Conditions;

enum ComparisonOperator: string
{
    case EQUAL = '=';
    case NOT_EQUAL = '<>';

    case GREATER = '>';
    case GREATER_EQUAL = '>=';

    case LESS = '<';
    case LESS_EQUAL = '<=';

    case IN = 'IN';
    case NOT_IN = 'NOT IN';

    case LIKE = 'LIKE';
    case NOT_LIKE = 'NOT LIKE';
}
