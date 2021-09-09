<?php
namespace App\Events;

use App\Entity\User;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class PasswordEncoderSubscriber implements EventSubscriberInterface {

    /**
     * Undocumented variable
     *
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
         $this->passwordHasher = $passwordHasher;
    }

    public static function getSubscribedEvents()
    {

        return [
            KernelEvents::VIEW =>['encodePassword', EventPriorities::PRE_WRITE]
        ];

    }
    public function encodePassword(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if($user instanceof User && $method === "POST"){

            $hash = $this->passwordHasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hash);
        }
    }
}

