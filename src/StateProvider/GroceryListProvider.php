<?php

namespace App\StateProvider;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Doctrine\Extension\CurrentUserExtension;
use App\Entity\GroceryList;
use App\Repository\GroceryListRepository;

final class GroceryListProvider implements ProviderInterface
{
    public function __construct(
        private readonly CurrentUserExtension $currentUserExtension,
        private readonly ProviderInterface $collectionProvider,
        private readonly GroceryListRepository $groceryListRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$operation instanceof CollectionOperationInterface) {
            return null;
        }

        $this->currentUserExtension->applyToCollection(
            $this->groceryListRepository->createQueryBuilder('gl'),
            new QueryNameGenerator(),
            GroceryList::class,
            $operation,
            $context
        );

        return $this->collectionProvider->provide($operation, $uriVariables, $context);
    }
}
