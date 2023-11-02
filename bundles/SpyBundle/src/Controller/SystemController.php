<?php

namespace SpyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
