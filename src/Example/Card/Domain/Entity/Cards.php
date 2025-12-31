<?php

namespace App\Example\Card\Domain\Entity;

final class Cards
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $front,
        public readonly string $back,
        public readonly int $user_id,
        public readonly int $list_id,
        public readonly int $review_count,
        public readonly int $reviewed,
        public readonly int $day_review,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly bool $deleted = false,
    ){}
}