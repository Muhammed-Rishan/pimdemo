<?php

namespace SpyBundle\MessageHandler;

use SpyBundle\Message\DPE;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

//use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class DPEHandler
{

    public function __invoke(DPE $message): void
    {
        // ... do some work
        $content = $message->getContent();

//        $this->logger->info('DPE message processed: ' . $content);
    }
}
