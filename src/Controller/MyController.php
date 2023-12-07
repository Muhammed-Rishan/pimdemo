<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DeviceDetector\DeviceDetector;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    /**
     * @Route("/device", name="your_action")
     */
    public function yourAction(Request $request): JsonResponse
    {
        $device = \Pimcore\Tool\DeviceDetector::getInstance();

//        $forceDeviceType = $request->query->get('forceDeviceType');
//
//        // Check for the 'forceDeviceType' query parameter and override the detected device type
//        if ($forceDeviceType === 'desktop') {
//            $device->setDeviceType(DeviceDetector::DEVICE_DESKTOP);
//        } elseif ($forceDeviceType === 'tablet') {
//            $device->setDeviceType(DeviceDetector::DEVICE_TABLET);
//        } elseif ($forceDeviceType === 'phone') {
//            $device->setDeviceType(DeviceDetector::DEVICE_PHONE);
//        }

        $deviceType = $device->getDevice();

        if ($device->isDesktop() || $device->isTablet()) {
            $response = ['message' => 'Handling logic for desktop or tablet'];
        } elseif ($device->isPhone()) {
            $response = ['message' => 'Handling logic for phones'];
        } else {
            $response = ['message' => 'Unknown device type'];
        }

        return $this->json($response);
    }
}
