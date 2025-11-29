<?php

namespace App\Example\Card\Domain\Repository\List;

interface ListInterface
{
    public function createList(int $user_id, string $title): bool;
    public function getListByUserId($user_id);
}