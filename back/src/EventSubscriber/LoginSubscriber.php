<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Exception\UserNotVerifiedException;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        if ($user instanceof User && !$user->isVerified()) {
            throw new UserNotVerifiedException();
        }
    }
}
