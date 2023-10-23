<?php

namespace App\Controller;


use App\Model\DataObject\Test;
use Pimcore\Bundle\ApplicationLoggerBundle\FileObject;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Hotel;
use Pimcore\Model\DataObject\Room;
use Pimcore\Model\Document\Link;
use Pimcore\Model\User;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Collectiontest;
use Pimcore\Model\DataObject\Fieldcollection\Data\MyCollection;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Pimcore\Model;
use Pimcore\SystemSettingsConfig;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Tool\DeviceDetector;





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

    private ApplicationLogger $logger;

    public function __construct(ApplicationLogger $logger)
    {
        $this->logger = $logger;
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
    public function testAction(Request $request, array $websiteConfig, ApplicationLogger $logger): Response
    {


        global $config;
        $test = Hotel::getById(id: 7);
        $tests = Room::getById(id: 4);

        $id = Link::getById(19);
        $link = $id->getHref();

        $translates = $tests->getDescri('hi');
        $translate = $test->getDescriptions('fr');

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
                $videoElement->setId(($videoData instanceof \Pimcore\Model\Asset) ? $videoData->getId(11) : $videoData);

            }
        }


        $config = SystemSettingsConfig::get();
        $bar = $config['general']['valid_languages'];

//        $brandingColor = $config['branding']['color_login_screen'];

//        $recaptchaKeyPublic = $websiteConfig['recaptchaPublic'];

//        $websiteConfig = pimcore_website_config();

        $note = new Model\Element\Note();
        $note->setElement($test);
        $note->setDate(time());
        $note->setType("erp_import");
        $note->setTitle("changed availabilities to xyz");
        $note->setUser(0);


        $note->addData("myText", "text", "Some Text");
        $note->addData("myObject", "object", Model\DataObject::getById(7));
        $note->addData("myDocument", "document", Model\Document::getById(15));
        $note->addData("myAsset", "asset", Model\Asset::getById(9));
        $note->save();


//        create a new user for Sydney

//        $user = User::create([
//            "parentId" => 0,
//            "username" => "testdemo",
//            "password" => "passwords",
//            "hasCredentials" => true,
//            "active" => true
//        ]);
//
//        $user->save();
//
//        $object = new Hotel();
//        $object->setUser($user->getId());
//        $object->save();

//        $objects = \Pimcore\Model\DataObject\Service::getObjectsReferencingUser(17);

        $this->logger->error('Your error message');
        $this->logger->alert('Your alert');
        $this->logger->debug('Your debug message', ['foo' => 'bar']);

        $fileObject = new FileObject('some interesting data');
        $myObject   = Room::getById(4);

        $logger->error('my error message', [
            'fileObject'    => $fileObject,
            'relatedObject' => $myObject,
            'component'     => 'different component',
            'source'        => 'Stack trace or context-relevant information'
        ]);



        $testObject = new Test();
        $testObject->setAttribute('translate language');
        $customValue = $testObject->getAttribute();

        $customRoom = new Room();
        $customRoom->setNewAttribute("This is a custom attribute value.");
        $customParent =  $customRoom->getNewAttribute();



        $tableData = $tests->getTables();

        $blockItems = $test->getBlock();
        $firstBlockItem = $blockItems[0];
        $roomnumber = $firstBlockItem["roomnumber"]->getData();
        $imageGalleryData = $test->getImages();
        $structuredTable = $tests->getTable();
        $rows = $structuredTable->getData();

        $languages = \Pimcore\Tool::getValidLanguages();
        \Pimcore\Model\DataObject\Localizedfield::setGetFallbackValues(false);
        $locale = '';
        $contents = $test->getName($locale);
        $contents = $test->getDescription($locale);

        $geopoint = $test->getGeopoint();

        return $this->render('test.html.twig', [

            'geopoint' => $geopoint,
            'roomnumber' => $roomnumber,
            'Contents' => $contents,
            'structuredTableData' => $rows,
            'imageGalleryData' => $imageGalleryData,
            'tableData' => $tableData,
            'classificationStoreData' => $classificationStoreData,
            'videoElement' => $videoElement,
            'link' =>$link,
            'translate'=>$translate,
            'translates'=>$translates,
            'bar' => $bar,
            'customValue' => $customValue,
            'customParent' => $customParent,

        ]);
    }

}
