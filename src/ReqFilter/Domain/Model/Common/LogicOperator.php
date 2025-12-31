<?php

namespace App\ReqFilter\Domain\Model\Common;

enum LogicOperator: string
{
    case OR = 'or';
    case AND = 'and';
}
