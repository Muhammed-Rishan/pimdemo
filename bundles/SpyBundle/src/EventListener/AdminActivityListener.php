<?php

// src/VendorName/SpyBundle/EventListener/AdminActivityListener.php
namespace SpyBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Pimcore\Security\User\TokenStorageUserResolver;
use SpyBundle\Model\AdminActivity;
use SpyBundle\Model\AdminActivity\Dao;
use Pimcore\Logger;
use Doctrine\DBAL\Exception;

class AdminActivityListener implements EventSubscriberInterface
{
    private $logger;
    protected TokenStorageUserResolver $userResolver;

    public function __construct(TokenStorageUserResolver $userResolver, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->userResolver = $userResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest()) {
            return;
        }

        // Check if the request path contains '/admin' to log only backend admin panel activity.
        if (strpos($request->getPathInfo(), '/admin') !== false) {
            // Determine the action
            $action = $this->determineAction($request);

            // Log the activity
            $this->logAdminActivity($request, $action);
        }
    }

    protected function logAdminActivity(Request $request, $action): void
    {
        $adminUserId = $this->extractAdminUserId();
        $timestamp = new \DateTime();

        $formattedTimestamp = $timestamp->format('Y-m-d H:i:s');

        $activity = AdminActivity::create($adminUserId, $action);
        $activity->setTimestamp($timestamp);
        $activity->save();

        // Use the Pimcore logger to log the activity
        Logger::info("Admin Activity - User ID: $adminUserId, Action: $action, Timestamp: $formattedTimestamp");
    }

    protected function extractAdminUserId(): ?int
    {
        // Implement your logic to extract the admin user ID.
        // You can use the TokenStorage or other sources to get the admin user ID.
        $user = $this->userResolver->getUser();

        if ($user) {
            return $user->getId();
        }

        return 0; // Set a default value if the admin user is not found.
    }

    protected function determineAction(Request $request): string
    {

        $pathInfo = $request->getPathInfo();

        // Modify this logic to suit your specific requirements.
        if (strpos($pathInfo, '/admin/document') !== false) {
            return 'document';
        } elseif (strpos($pathInfo, '/admin/asset') !== false) {
            return 'asset';
        } elseif (strpos($pathInfo, '/admin/object') !== false) {
            return 'objects';
        } elseif (strpos($pathInfo, '/admin/classes') !== false) {
            return 'classes';
        } elseif (strpos($pathInfo, '/admin/login') !== false) {
            return 'login';
        } elseif (strpos($pathInfo, '/admin/#') !== false) {
            return 'logout';
        } else {
            return 'unknown'; // You can set a default value for other cases.
        }
    }
}
