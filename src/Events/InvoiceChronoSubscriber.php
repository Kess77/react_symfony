<?php

namespace App\Events;

use App\Entity\User;
use DateTimeInterface;
use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvoiceCustomerSubscriber implements EventSubscriberInterface{

    private $security;
    private $repository;

    public function __construct(Security $security, InvoiceRepository $repository)
    {
        $this->security = $security;
        $this->repository = $repository;
    }

   

     public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW =>['setChronoForInvoice', EventPriorities::PRE_VALIDATE]
        ];

    }
    
    public function setChronoForInvoice (ViewEvent $event)
    {
        

        // 1.  Trouver 'utilisateur actuellement connecté (Security)
    
        // 2.  Le repository des factures (InvoicesRepossitory)
    
        // 3.  Choper la dérnière facture qui a été inserée , et son chrono 
    
        // 4.  On rajoute +1 le chrono 

        $invoice = $event->getControllerResult();
        $method  = $event->getRequest()->getMethod();

        if($invoice instanceof Invoice && $method === "POST" )
        {
            $nextChrono = $this->repository->findNextChrono($this->security->getUser());
            $invoice->setChrono($nextChrono);


            if(empty($invoice->getSentAt()))
            {
                $invoice->setSentAt(new \DateTime());
            }
        }

    }
}