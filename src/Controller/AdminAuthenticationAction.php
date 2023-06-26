<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

final class AdminAuthenticationAction
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthenticatorInterface $authenticator,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(Request $request, UserAuthenticatorInterface $userAuthenticator): Response
    {
        $credentials = json_decode($request->getContent());

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['username' => $credentials->username]);

        if (null === $user || !$this->passwordHasher->isPasswordValid($user, $credentials->password)) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Invalid credentials.'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!$user->hasRole('ROLE_ADMIN')) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_FORBIDDEN,
                    'message' => 'Forbidden.'
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        return $userAuthenticator->authenticateUser($user, $this->authenticator, $request);
    }
}
