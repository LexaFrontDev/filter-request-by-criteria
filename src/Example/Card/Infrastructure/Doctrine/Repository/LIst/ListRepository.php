<?php

namespace App\Example\Card\Infrastructure\Doctrine\Repository\LIst;



use App\Example\Card\Domain\Entity\Lists;
use App\ReqFilter\Contracts\FilterInterface;
use App\Example\Card\Domain\Repository\List\ListInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Example\Card\Infrastructure\Doctrine\Entity\ListsEntity;
use App\Example\Card\Infrastructure\Doctrine\Criterias\Mappers\FleshList\ReqFleshListMapper;


/**
 * @extends ServiceEntityRepository<ListsEntity>
 */
final class ListRepository extends ServiceEntityRepository implements ListInterface
{
    private FilterInterface $filter;
    private ReqFleshListMapper $reqFleshListMapper;

    private EntityManagerInterface $em;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $em,
        FilterInterface $filter,
        ReqFleshListMapper $reqFleshListMapper
    ) {
        parent::__construct($registry, ListsEntity::class);
        $this->em = $em;
        $this->filter = $filter;
        $this->reqFleshListMapper = $reqFleshListMapper;
    }



    public function createList(int $user_id, string $title): bool
    {
        $listDto = new Lists(
            user_id: $user_id,
            title: $title,
            createdAt:  new \DateTimeImmutable(),
            deleted: false
        );

        $obj = new ListsEntity(list:$listDto);
        $this->em->persist($obj);
        $this->em->flush();
        return true;
    }

    public function getListByUserId($user_id)
    {
        $dto = $this->reqFleshListMapper->toDto($user_id);
        $req = $this->filter->initFilter(criteriasDto: $dto, tableName: 'lists', alias: 'l');
        return $req->getList();
    }
}