<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

#[AsController]
class UserFavoritesAction
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function __invoke(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new AccessDeniedException();
        }

        return $user->getFavorites()->toArray();
    }
}
