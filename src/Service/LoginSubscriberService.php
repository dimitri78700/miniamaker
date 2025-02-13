<?php
// src/Service/LoginSubscriberService.php
namespace App\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class LoginSubscriberService implements EventSubscriberInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (is_object($user)) {
            $email = (new Email())
                ->from('contact@miniamaker.fr')
                ->to((string) $user->getEmail())
                ->subject('Connexion réussie')
                ->text('Bonjour ' . $user->getUsername() . ', vous venez de vous connecter à votre compte.');
                
            $this->mailer->send($email);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onInteractiveLogin',
        ];
    }
}

