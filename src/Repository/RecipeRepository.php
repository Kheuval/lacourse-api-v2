<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function getSample(): array
    {
        $adminUser = $this->userRepository->find(1);

        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :adminUser')
            ->orderBy('RAND()')
            ->setMaxResults(5)
            ->setParameter('adminUser', $adminUser)
            ->getQuery()
            ->execute()
        ;
    }

    public function findAllForAdminUserOrCurrentUser(UserInterface $user): array
    {
        $adminUser = $this->userRepository->find(1);

        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->orWhere('r.user = :adminUser')
            ->setParameters([
                'user' => $user,
                'adminUser' => $adminUser
            ])
            ->getQuery()
            ->execute()
        ;
    }

    public function findAllForUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->setParameters([
                'user' => $user,
            ])
            ->getQuery()
            ->execute()
        ;
    }
}
