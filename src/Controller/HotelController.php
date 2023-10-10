<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Hotel;
use Pimcore\Model\DataObject\Room;
use Pimcore\Model\Document\Link;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Collectiontest;
use Pimcore\Model\DataObject\Fieldcollection\Data\MyCollection;
use Symfony\Component\Routing\Annotation\Route;

class HotelController extends FrontendController
{

    public function hotelAction(Request $request): Response
    {
        return $this->render('hotel/hotel.html.twig');
    }
    #[Template('hotel/gallery.html.twig')]
    public function galleryAction(Request $request): array
    {
        if ('asset' === $request->get('type')) {
            $asset = Asset::getById((int) $request->get('id'));
            if ('folder' === $asset->getType()) {
                return [
                    'assets' => $asset->getChildren()
                ];
            }
        }

        return [];
    }
    public function footerAction(Request $request): Response
    {
        return $this->render('footer.html.twig');
    }
    public function demoAction(Request $request): Response
    {
        return $this->render('demo.html.twig');
    }

    /**
     * @throws \Exception
     */
    public function testAction(Request $request): Response
    {

        $test = Hotel::getById(id: 7);
        $tests = Room::getById(id: 4);

        $id = Link::getById(19);
        $link = $id->getHref();


        $class = ClassDefinition::getById(4);
        $fields = $class->getFieldDefinitions();

        foreach ($fields as $field) {
            $field->setLocked(true);
        }
        $class->save();

        $hotel = \Pimcore\Model\DataObject\Room::getById(4);
        $hotelBrick = $hotel->getSuite();

        if ($hotelBrick === null) {
            throw $this->createNotFoundException('Employee not found');
        }


        $classificationStore = $test->getMyhotel();

        foreach ($classificationStore->getGroups() as $group) {
            $groupData = [
                'groupName' => $group->getConfiguration()->getName(),
                'keys' => []
            ];

            foreach ($group->getKeys() as $key) {
                $keyConfiguration = $key->getConfiguration();

                $value = $key->getValue();
                if ($value instanceof \Pimcore\Model\DataObject\Data\QuantityValue) {
                    $value = (string) $value;
                }

                $groupData['keys'][] = [
                    'id' => $keyConfiguration->getId(),
                    'name' => $keyConfiguration->getName(),
                    'value' => $value,
                    'isQuantityValue' => ($key->getFieldDefinition() instanceof QuantityValue),
                ];
            }

            $classificationStoreData[] = $groupData;
        }


        $videoElement = null;
        if ($test->getVideo()) {
            $video = $test->getVideo();
            $videoData = $video->getData();

            if ($videoData) {
                $videoElement = new \Pimcore\Model\Document\Editable\Video();
                $videoElement->setConfig([
                    "thumbnail" => "content",
                    "width" => "400",
                    "height" => 300,
                    "attributes" => [
                        "class" => "video-js custom-class",
                        "preload" => "auto",
                        "controls" => "",
                        "data-custom-attr" => "my-test"
                    ]
                ]);
                $videoElement->setType($video->getType());
                $videoElement->setTitle($video->getTitle());
                $videoElement->setDescription($video->getDescription());
                $videoElement->setId(($videoData instanceof \Pimcore\Model\Asset) ? $videoData->getId() : $videoData);

            }
        }






        $tableData = $tests->getTables();

        $blockItems = $test->getBlock();
        $firstBlockItem = $blockItems[0];
        $roomnumber = $firstBlockItem["roomnumber"]->getData();
        $imageGalleryData = $test->getImages();
        $structuredTable = $tests->getTable();
        $rows = $structuredTable->getData();

        $languages = \Pimcore\Tool::getValidLanguages();
        \Pimcore\Model\DataObject\Localizedfield::setGetFallbackValues(false);
        $locale = 'de';
        $content = $test->getName($locale);
        $content = $test->getDescription($locale);
        $geopoint = $test->getGeopoint();

        return $this->render('test.html.twig', [
            'geopoint' => $geopoint,
            'roomnumber' => $roomnumber,
            'Content' => $content,
            'structuredTableData' => $rows,
            'imageGalleryData' => $imageGalleryData,
            'tableData' => $tableData,
            'classificationStoreData' => $classificationStoreData,
            'videoElement' => $videoElement,
            'link' =>$link,

        ]);
    }

}
