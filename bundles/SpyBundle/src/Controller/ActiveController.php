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

//    /**
//     * @Route("/system", name="system")
//     */
//    public function saveDataAction(Request $request): JsonResponse
//    {
//        // Retrieve data from the request
//        $data = json_decode($request->get('data'), true);
//
//        if ($data === null) {
//            return new JsonResponse(['success' => false, 'message' => 'Invalid data']);
//        }
//
//        // Validate and process the data as needed
//        $configPath = '/var/config/system_settings/systems.yaml';
//
//        // Check if the config file exists
//        if (file_exists($configPath)) {
//            $yamlData = Yaml::dump($data, 4);
//
//            // Save the updated configuration data to the systems.yaml file
//            file_put_contents($configPath, $yamlData);
//
//            // Respond with a success message
//            return new JsonResponse(['success' => true, 'message' => 'Data saved successfully']);
//        } else {
//            // Respond with an error message
//            return new JsonResponse(['success' => false, 'message' => 'System configuration file not found']);
//        }
//    }
}
