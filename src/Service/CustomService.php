<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class CustomService
{
    private LoggerInterface $customLogLogger;

    public function __construct(LoggerInterface $customLogLogger)
    {
        $this->customLogLogger = $customLogLogger;
    }

    public function someMethod(): void
    {
        $this->customLogLogger->debug('Test Message');
    }
}
