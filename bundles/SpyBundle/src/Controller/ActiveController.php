<?php


namespace SpyBundle\Controller;

use Doctrine\DBAL\Exception;
use SpyBundle\Model\AdminActivity\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use SpyBundle\Model\AdminActivity\Dao;

class ActiveController extends AbstractController
{

    /**
     * @Route("/spy", name="spy", methods={"GET"})
     * @throws Exception
     */
    public function spyAction(): JsonResponse
    {
        $listing = new Listing();

        $data = $listing->load();

        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'id' => $item->getId(),
                'adminuserid' => $item->getAdminUserId(),
                'action' => $item->getAction(),
                'timestamp' => $item->getTimestamp(),
            ];
        }

        return new JsonResponse(['data' => $formattedData]);
    }
}
