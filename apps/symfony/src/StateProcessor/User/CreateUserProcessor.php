<?php

namespace App\StateProcessor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

final class CreateUserProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $decorated,
        private readonly UserProcessor $userProcessor,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $data = $this->userProcessor->encodePassword($data);

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
