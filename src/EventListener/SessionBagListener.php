<?php

// src/EventListener/SessionBagListener.php

// src/EventListener/SessionBagListener.php

namespace App\EventListener;


use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SessionBagListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 127],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($event->getRequest()->attributes->get('_stateless', false)) {
            return;
        }

        $session = $event->getRequest()->getSession();

        // Do not register bags if the session is already started
        if ($session->isStarted()) {
            return;
        }

        $bag = new AttributeBag('_session_cart');
        $bag->setName('session_cart');

        $session->registerBag($bag);
    }


//use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
//use Symfony\Component\HttpFoundation\Session\SessionInterface;
//use Symfony\Component\HttpKernel\Event\RequestEvent;
//use Symfony\Component\HttpKernel\KernelEvents;
//use Symfony\Component\EventDispatcher\EventSubscriberInterface;
//
//class SessionBagListener implements EventSubscriberInterface
//{
//    public static function getSubscribedEvents(): array
//    {
//        return [
//            KernelEvents::REQUEST => ['onKernelRequest', 127],
//        ];
//    }
//
//    public function onKernelRequest(RequestEvent $event): void
//    {
//        if (!$event->isMainRequest()) {
//            return;
//        }
//
//        if ($event->getRequest()->attributes->get('_stateless', false)) {
//            return;
//        }
//
//        $session = $event->getRequest()->getSession();
//
//        // Check if the session bag is already registered
//        if ($session->isStarted() && !$session->getBag('session_cart')) {
//            $bag = new AttributeBag('_session_cart');
//            $bag->setName('session_cart');
//            $session->registerBag($bag);
//        }
//    }


}
