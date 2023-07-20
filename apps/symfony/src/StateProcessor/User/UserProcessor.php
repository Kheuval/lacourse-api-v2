<?php

declare(strict_types=1);

namespace App\StateProcessor\User;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProcessor
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder,
    ) {}

    public function encodePassword(User $data): User
    {
        $data->setPassword(
            $this->passwordEncoder->hashPassword(
                $data,
                $data->getPlainPassword()
            )
        );

        $data->eraseCredentials();

        return $data;
    }
}
