<?php

namespace App\ReqFilter\CriteriaDto\Common;

enum LogicOperator: string
{
    case OR = 'or';
    case AND = 'and';
}
