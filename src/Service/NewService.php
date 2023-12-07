<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class NewService
{
    private LoggerInterface $myLogLogger;

    public function __construct(LoggerInterface $myLogLogger)
    {
        $this->myLogLogger = $myLogLogger;
    }

    public function someMethod(): void
    {
        $this->myLogLogger->debug('Test Message');
    }
}
