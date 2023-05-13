<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly string $frontUrl
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreateTokenEvent::class => 'onCreateToken',
            UpdatePasswordEvent::class => 'onUpdatePassword',
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onCreateToken(CreateTokenEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();

        /** @var User $user */
        $user = $passwordToken->getUser();

        $message = (new TemplatedEmail())
            ->from(new Address('contact@lacourse.shop', 'La Course'))
            ->to(new Address($user->getEmail(), $user->getUsername()))
            ->subject('Réinitialisation du mot de passe')
            ->htmlTemplate('email/forgotPassword.html.twig')
            ->context([
                'frontUrl' => $this->frontUrl,
                'token' => $passwordToken->getToken()
            ])
        ;

        if (0 === $this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send email');
        }
    }

    public function onUpdatePassword(UpdatePasswordEvent $event): void
    {
        $user = $event->getPasswordToken()->getUser();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $event->getPassword());
        $this->userRepository->upgradePassword($user, $hashedPassword);
    }
}
