<?php

namespace App\ReqFilter\Domain\Model\Join;

enum JoinType: string
{
    case LEFT = 'LEFT';
    case INNER = 'INNER';
    case RIGHT = 'RIGHT';
}