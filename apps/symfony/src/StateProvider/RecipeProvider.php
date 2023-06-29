<?php

declare(strict_types=1);

namespace App\StateProvider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

final class RecipeProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly UserRepository $userRepository,
        private readonly RecipeRepository $recipeRepository,
        private readonly ProviderInterface $collectionProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($operation instanceof CollectionOperationInterface) {
            if (!array_key_exists('filters', $context)) {
                return $this->recipeRepository->findAllForAdminUserOrCurrentUser($user);
            }

            if (array_key_exists('user', $context['filters'])) {
                $user = $this->userRepository->find($context['filters']['user']);
                return $this->recipeRepository->findAllForUser($user);
            }

            return $this->collectionProvider->provide($operation, $uriVariables, $context);
        }

        if ($operation instanceof Get) {
            $recipe = $this->recipeRepository->find($uriVariables['id']);

            if ($user === $recipe->getUser() || $recipe->getUser()->getId() === 1) {
                return $recipe;
            } else {
                throw new AccessDeniedException();
            }
        }

        return null;
    }
}
