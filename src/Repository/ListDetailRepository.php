<?php

namespace App\Repository;

use App\Entity\ListDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListDetail[]    findAll()
 * @method ListDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListDetail::class);
    }
}
