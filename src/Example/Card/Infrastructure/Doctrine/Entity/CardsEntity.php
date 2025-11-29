<?php

namespace App\Example\Card\Infrastructure\Doctrine\Entity;

use App\Example\Card\Domain\Entity\Cards;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cards')]
class CardsEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $front;

    #[ORM\Column(type: 'text')]
    private string $back;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'integer')]
    private int $listId;

    #[ORM\Column(type: 'integer')]
    private int $reviewCount;

    #[ORM\Column(type: 'integer')]
    private int $reviewed;

    #[ORM\Column(type: 'integer')]
    private int $dayReview;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'boolean')]
    private bool $deleted = false;

    public function __construct(
        string $front,
        string $back,
        int $userId,
        int $listId,
        int $reviewCount = 0,
        int $reviewed = 0,
        int $dayReview = 0,
        bool $deleted = false
    ) {
        $this->front = $front;
        $this->back = $back;
        $this->userId = $userId;
        $this->listId = $listId;
        $this->reviewCount = $reviewCount;
        $this->reviewed = $reviewed;
        $this->dayReview = $dayReview;
        $this->deleted = $deleted;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }


    public function asDto(): Cards
    {
        return new Cards(
            id: $this->id,
            front: $this->front,
            back: $this->back,
            user_id: $this->userId,
            list_id: $this->listId,
            review_count: $this->reviewCount,
            reviewed: $this->reviewed,
            day_review: $this->dayReview,
            createdAt: $this->createdAt->format('Y-m-d H:i:s'),
            updatedAt: $this->updatedAt->format('Y-m-d H:i:s'),
            deleted: $this->deleted,
        );
    }

    public function fromDto(Cards $dto): self
    {
        if (!is_null($dto->id)) $this->id = $dto->id;
        $this->front = $dto->front;
        $this->back = $dto->back;
        $this->userId = $dto->user_id;
        $this->listId = $dto->list_id;
        $this->reviewCount = $dto->review_count;
        $this->reviewed = $dto->reviewed;
        $this->dayReview = $dto->day_review;
        $this->deleted = $dto->deleted;
        $this->createdAt = new \DateTimeImmutable($dto->createdAt);
        $this->updatedAt = new \DateTimeImmutable($dto->updatedAt);

        return $this;
    }


    public function getId(): ?int { return $this->id; }
    public function getFront(): string { return $this->front; }
    public function getBack(): string { return $this->back; }
    public function getUserId(): int { return $this->userId; }
    public function getListId(): int { return $this->listId; }
    public function getReviewCount(): int { return $this->reviewCount; }
    public function getReviewed(): int { return $this->reviewed; }
    public function getDayReview(): int { return $this->dayReview; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function isDeleted(): bool { return $this->deleted; }


    public function incrementReviewCount(): void
    {
        $this->reviewCount++;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function incrementReviewed(): void
    {
        $this->reviewed++;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function setDayReview(int $dayReview): void
    {
        $this->dayReview = $dayReview;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markDeleted(): void
    {
        $this->deleted = true;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
