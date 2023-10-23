<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\Asset;

class AssetController extends AbstractController
{
    /**
     * Create and save a new asset.
     *
     * @Route("/create-asset", name="create_asset")
     * @throws \Exception
     */
    public function createAsset(): JsonResponse
    {
        $newAsset = new Asset();
        $newAsset->setParentId(1);
        $newAsset->setFilename("miImage.png");
        $newAsset->setData(file_get_contents($_SERVER['DOCUMENT_ROOT']. '/image/tom.png'));
        $newAsset->setParent(Asset::getByPath("/image"));

        $newAsset->save(["versionNote" => "my new version"]);

        return new JsonResponse(['message'=> 'Success']);
    }

    /**
     * Update an asset.
     *
     * @Route("/update-asset/{id}", name="update_asset")
     * @throws \Exception
     */
    public function updateAsset(): JsonResponse
    {
        $asset = Asset::getById(20);

        if ($asset) {
            $asset->setData(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/image/bike1.jpeg'));
            $asset->save(["versionNote" => "Updated version"]);

            return new JsonResponse(['message' => 'Asset updated']);
        }

        return new JsonResponse(['message' => 'Asset not found'], 404);
    }

    /**
     * Delete an asset.
     *
     * @Route("/delete-asset/{id}", name="delete_asset")
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteAsset(): JsonResponse
    {
        $asset = Asset::getById(26);

        if ($asset) {
            $asset->delete();

            return new JsonResponse(['message' => 'Asset deleted']);
        }

        return new JsonResponse(['message' => 'Asset not found'], 404);
    }
}
