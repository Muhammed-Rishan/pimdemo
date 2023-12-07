<?php

// src/Message/SmsNotification.php
namespace ConnectBundle\Messenger;

class SmsNotification
{
    public function __construct(private string $content)
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
