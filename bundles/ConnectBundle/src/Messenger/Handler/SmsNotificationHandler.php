<?php
//
//// src/MessageHandler/SmsNotificationHandler.php
//namespace ConnectBundle\Messenger\Handler;
//
//use ConnectBundle\Messenger\SmsNotification;
//use ConnectBundle\Service\SmsService;
//use Symfony\Component\Messenger\Attribute\AsMessageHandler;
//
//#[AsMessageHandler]
//class SmsNotificationHandler
//{
//    private SmsService $smsService;
//
//    public function __construct(SmsService $smsService)
//    {
//        $this->smsService = $smsService;
//    }
//
//    public function __invoke(SmsNotification $message)
//    {
//        $smsContent = $message->getContent();
//
//        // Replace '123456789' with the actual phone number where you want to send the SMS.
//        $phoneNumber = '123456789';
//
//        // Use the SmsService to send the SMS.
//        $this->smsService->sendSms($phoneNumber, $smsContent);
//    }
//}
// src/ConnectBundle/Messenger/Handler/SmsNotificationHandler.php
namespace ConnectBundle\Messenger\Handler;

use ConnectBundle\Messenger\SmsNotification;
use ConnectBundle\Service\SmsService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SmsNotificationHandler
{
    private SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function __invoke(SmsNotification $message): void
    {
        $smsContent = $message->getContent();

        $phoneNumber = '9562411344';

        $this->smsService->sendSms($phoneNumber, $smsContent);
    }
}

