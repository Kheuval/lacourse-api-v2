<?php

declare(strict_types=1);

namespace App\StateProcessor\Recipe;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Recipe;
use App\Repository\MediaObjectRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Security\Core\Security;

final class CreateRecipeProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface    $decorated,
        private readonly Security              $security,
        private readonly IngredientRepository  $ingredientRepository,
        private readonly MediaObjectRepository $imageRepository,
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        /** @var Recipe $data */
        $data->setUser($this->security->getUser());

        if (!$data->getImage()) {
            $data->setImage($this->imageRepository->findPlaceholderImage());
        }

        foreach ($data->getRecipeIngredients() as $recipeIngredient) {
            $ingredient = $this->ingredientRepository->findOneBy(['name' => $recipeIngredient->getIngredient()->getName()]);

            if ($ingredient !== null) {
                $data->removeRecipeIngredient($recipeIngredient);
                $data->addRecipeIngredient($recipeIngredient->setIngredient($ingredient));
            } else {
                $recipeIngredient->getIngredient()->setIsEdible(true);
            }
        }

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
