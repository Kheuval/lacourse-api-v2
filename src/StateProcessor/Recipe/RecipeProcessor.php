<?php

declare(strict_types=1);

namespace App\StateProcessor\Recipe;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

final class RecipeProcessor implements ProcessorInterface
{
    public function __construct(

    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        dd($operation);
    }
}
