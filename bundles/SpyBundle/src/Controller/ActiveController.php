<?php


namespace SpyBundle\Controller;

use Doctrine\DBAL\Exception;
use SpyBundle\Model\AdminActivity\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use SpyBundle\Model\AdminActivity\Dao;
use Symfony\Component\Yaml\Yaml;

class ActiveController extends AbstractController
{

    /**
     * @Route("/spy", name="spy", methods={"GET"})
     * @param Request $request
     * @throws Exception
     */
    public function spyAction(Request $request): JsonResponse
    {

        $listing = new Listing();

        $data = $listing->load();

        $totalRecords = count($data);

        $page = $request->query->get('page', 1);
        $pageSize = 500;
        $offset = ($page - 1) * $pageSize;
        $pagedData = array_slice($data, $offset, $pageSize);

        $formattedData = [];
        foreach ($pagedData as $item) {
            $formattedData[] = [
                'id' => $item->getId(),
                'adminuserid' => $item->getAdminUserId(),
                'action' => $item->getAction(),
                'timestamp' => $item->getTimestamp(),
            ];
        }

        return new JsonResponse([
            'total' => $totalRecords,
            'data' => $formattedData,
        ]);
    }

}
