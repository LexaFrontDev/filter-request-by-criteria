<?php

namespace App\Example\User\Domain\Entity;

final class User
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role = 'ROLE_USER',
        public readonly bool $active = true,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly bool $deleted = false,
    ){}
}