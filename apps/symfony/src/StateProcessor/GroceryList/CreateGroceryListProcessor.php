<?php

declare(strict_types=1);

namespace App\StateProcessor\GroceryList;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GroceryList;
use App\Entity\ListDetail;
use App\Repository\IngredientRepository;
use Symfony\Component\Security\Core\Security;

final class CreateGroceryListProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $decorated,
        private readonly Security $security,
        private readonly IngredientRepository $ingredientRepository,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        /** @var GroceryList $data */
        $data->setUser($this->security->getUser());

        /** @var ListDetail $listDetail */
        foreach ($data->getListDetails() as $listDetail) {
            $ingredient = $this->ingredientRepository->findOneBy(['name' => $listDetail->getIngredient()->getName()]);

            if (null !== $ingredient) {
                $data->removeListDetail($listDetail);
                $data->addListDetail($listDetail->setIngredient($ingredient));
            }
        }

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
