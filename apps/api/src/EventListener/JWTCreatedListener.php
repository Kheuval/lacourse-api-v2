<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class JWTCreatedListener
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly NormalizerInterface $itemNormalizer,
        private readonly IriConverterInterface $iriConverter,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $user = $this->userRepository->findOneBy(['username' => $payload['username']]);
        $payload['user'] = $this->itemNormalizer->normalize(
            [
                'id' => $this->iriConverter->getIriFromResource($user),
                'username' => $payload['username'],
                'email' => $user->getEmail(),
            ],
            'json'
        );

        unset($payload['roles']);

        $event->setData($payload);
    }
}
