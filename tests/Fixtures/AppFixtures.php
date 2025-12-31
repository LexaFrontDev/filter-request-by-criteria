<?php

namespace App\Tests\Fixtures;

use App\Example\Card\Domain\Entity\Cards;
use App\Example\Card\Infrastructure\Doctrine\Entity\CardsEntity;
use App\Example\User\Domain\Entity\User;
use App\Example\User\Infrastructure\Doctrine\Entity\UserEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        foreach (range(1, 5) as $i) {
            $userDto = new User(
                id: null,
                name: "User $i",
                email: "user$i@example.com",
                password: "password", 
                role: 'ROLE_USER'
            );
            $userEntity = new UserEntity($userDto);
            $hashedPassword = $this->hasher->hashPassword($userEntity, $userDto->password);
            $userEntity->setPassword($hashedPassword);
            
            $manager->persist($userEntity);
            $users[] = $userEntity;
        }

        foreach ($users as $index => $user) {
            foreach (range(1, 3) as $j) {
                $cardDto = new Cards(
                    id: null,
                    front: "Front $j User $index",
                    back: "Back $j User $index",
                    user_id: 1,
                    list_id: 1,
                    review_count: 0,
                    reviewed: 0,
                    day_review: 0
                );
            }
        }
        $manager->flush();
        
         foreach ($users as $user) {
            foreach (range(1, 3) as $j) {
                 $cardDto = new Cards(
                    id: null,
                    front: "Front $j for " . $user->getName(),
                    back: "Back $j for " . $user->getName(),
                    user_id: $user->getId(),
                    list_id: 1,
                    review_count: 0,
                    reviewed: 0,
                    day_review: 0
                );
                $cardEntity = new CardsEntity(
                    $cardDto->front,
                    $cardDto->back,
                    $cardDto->user_id,
                    $cardDto->list_id
                );
                $manager->persist($cardEntity);
            }
         }
         $manager->flush();
    }
}
