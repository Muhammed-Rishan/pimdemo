<?php


namespace SpyBundle\Service;

use Psr\Log\LoggerInterface;

class ApiService
{
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(string $apiKey, LoggerInterface $logger)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    public function authenticate(string $apiKey): bool
    {
        return $apiKey === $this->apiKey;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
