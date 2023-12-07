<?php

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

        if (str_contains($request->getPathInfo(), '/admin')) {
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

        Logger::info("Admin Activity - User ID: $adminUserId, Action: $action, Timestamp: $formattedTimestamp");
    }

    protected function extractAdminUserId(): ?int
    {
        $user = $this->userResolver->getUser();

        if ($user) {
            return $user->getId();
        }

        return 1;
    }

    protected function determineAction(Request $request): string
    {

        $pathInfo = $request->getPathInfo();

        if (str_contains($pathInfo, '/admin/document')) {
            return 'document';
        } elseif (str_contains($pathInfo, '/admin/asset')) {
            return 'asset';
        } elseif (str_contains($pathInfo, '/admin/object')) {
            return 'objects';
        } elseif (str_contains($pathInfo, '/admin/classes')) {
            return 'classes';
        } elseif (str_contains($pathInfo, '/admin/login')) {
            return 'login';
        } elseif (str_contains($pathInfo, '/admin/logout')) {
            return 'logout';
        } else {
            return 'unknown';
        }
    }
}
