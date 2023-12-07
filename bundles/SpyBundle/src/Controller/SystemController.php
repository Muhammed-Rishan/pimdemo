<?php

namespace SpyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends AbstractController
{

    /**
     * @Route("/save-data", name="saveData")
     */

    public function saveDataAction(Request $request): JsonResponse
    {
        $data = json_decode($request->get('data'), true);

        if ($data === null) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid data']);
        }

        $configPath = $this->getParameter('kernel.project_dir') . '/bundles/SpyBundle/config/systems.yaml';

        $yamlData = Yaml::dump($data, 4);

        file_put_contents($configPath, $yamlData);

        return new JsonResponse(['success' => true, 'message' => 'Data saved successfully']);
    }
    /**
     * @Route("/load-data", name="loadData")
     */
    public function loadDataAction(): JsonResponse
    {
        $configPath = $this->getParameter('kernel.project_dir') . '/bundles/SpyBundle/config/systems.yaml';

        if (file_exists($configPath)) {
            $yamlData = Yaml::parse(file_get_contents($configPath));

            return new JsonResponse(['success' => true, 'data' => $yamlData]);
        }

        return new JsonResponse(['success' => false, 'message' => 'System configuration file not found']);
    }


    /**
     * @Route("/data", name="Data")
     */
    public function showAction(Request $request): Response
    {
        $yamlFilePath = $this->getParameter('kernel.project_dir') . '/bundles/SpyBundle/config/systems.yaml';

        if (file_exists($yamlFilePath)) {
            $yamlData = file_get_contents($yamlFilePath);
            $parsedData = Yaml::parse($yamlData);
//            dump($parsedData);

            $isChecked = isset($parsedData['checkboxValue']) && $parsedData['checkboxValue'] === true;

            if ($isChecked) {

                return $this->render('@SpyBundle/spy.html.twig', ['parsedData' => $parsedData]);
            } else {
                return new JsonResponse(['error' => 'Access denied. Please check your permissions.']);
            }
        } else {
            return new JsonResponse(['error' => 'YAML file not found']);
        }
    }

}