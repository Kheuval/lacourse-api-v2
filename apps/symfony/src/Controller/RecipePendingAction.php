<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RecipePendingAction
{
    public function __construct(private readonly RecipeRepository $recipeRepository)
    {

    }

    public function __invoke(): array
    {
        return $this->recipeRepository->findAllPending();
    }
}
