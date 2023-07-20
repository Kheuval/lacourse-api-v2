<?php

namespace App\Repository;

use App\Entity\GroceryList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroceryList|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroceryList|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroceryList[]    findAll()
 * @method GroceryList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroceryListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroceryList::class);
    }
}
