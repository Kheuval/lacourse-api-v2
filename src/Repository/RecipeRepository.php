<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserRepository $userRepository,
    )
    {
        parent::__construct($registry, Recipe::class);
    }

    public function getSample()
    {
        $user = $this->userRepository->find(1);

        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->orderBy('RAND()')
            ->setMaxResults(5)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute()
            ;
    }
}
