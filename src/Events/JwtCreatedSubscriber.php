<?php

namespace App\Events;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber{

    /**
    * @var RequestStack
    */
    private $requestStack;

    /**
    * @param RequestStack $requestStack
    */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function updateJwtdata(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        // 1 - récuperer user pour avoir son firstname et lastname
        $user = $event->getUser();

        // 2 - Enrichir les datas ( on rajoute des propriétés dans le token)

        $data = $event->getData();

        $data['firstName'] = $user->getFirstName();
        $data['lastname'] = $user->getLastname();

        $event->setData($data);

    }

}