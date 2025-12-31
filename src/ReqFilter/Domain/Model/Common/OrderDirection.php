<?php

namespace App\ReqFilter\Domain\Model\Common;

enum OrderDirection: string
{
    case  ASC = 'ASC';
    case DESC = 'DESC';
}