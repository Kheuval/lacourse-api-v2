<?php

declare(strict_types=1);

namespace App\StateProcessor\GroceryList;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Security\Core\Security;

final class CreateGroceryListProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $decorated,
        private readonly Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $data->setUser($this->security->getUser());

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
