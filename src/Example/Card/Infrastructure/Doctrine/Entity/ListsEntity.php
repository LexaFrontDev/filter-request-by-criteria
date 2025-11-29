<?php

namespace App\Example\Card\Infrastructure\Doctrine\Entity;

use App\Example\Card\Domain\Entity\Lists;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'lists')]
class ListsEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'user_id', type: 'int', length: 255)]
    public readonly int $user_id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'boolean')]
    private bool $deleted = false;

    public function __construct(Lists $list)
    {
        $this->user_id = $list->user_id;
        $this->title = $list->title;
        $this->deleted = $list->deleted;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }


    public function asDto(): Lists
    {
        return new Lists(
            id: $this->id,
            user_id: $this->user_id,
            title: $this->title,
            createdAt: $this->createdAt->format('Y-m-d H:i:s'),
            updatedAt: $this->updatedAt->format('Y-m-d H:i:s'),
            deleted: $this->deleted
        );
    }

    public function fromDto(Lists $dto): self
    {
        if (!is_null($dto->id)) $this->id = $dto->id;
        $this->user_id = $dto->user_id;
        $this->title = $dto->title;
        $this->deleted = $dto->deleted;
        $this->createdAt = new \DateTimeImmutable($dto->createdAt);
        $this->updatedAt = new \DateTimeImmutable($dto->updatedAt);

        return $this;
    }


    public function getId(): ?int { return $this->id; }
    public function getUserId(): ?int { return $this->user_id; }
    public function getTitle(): string { return $this->title; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function isDeleted(): bool { return $this->deleted; }


    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markDeleted(): void
    {
        $this->deleted = true;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
