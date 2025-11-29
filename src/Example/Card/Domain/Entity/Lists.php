<?php

namespace App\Example\Card\Domain\Entity;

use DateTimeInterface;

final class Lists
{

    public function __construct(
        public readonly ?int $id = null,
        public readonly int $user_id,
        public readonly string $title,
        public readonly ?DateTimeInterface $createdAt = null,
        public readonly ?DateTimeInterface $updatedAt = null,
        public readonly bool $deleted = false,
    ){}

}