<?php
namespace ConnectBundle\Tool;

use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;


class Installer extends AbstractInstaller
{

    const CONFIGURATION_CLASS_NAME = 'Custom';

    public function install(): void
    {
        $filePath = __DIR__ . '/../../config/install/classes/class_Custom.json';
        $path = realpath($filePath);
        $class = new ClassDefinition();
        $class->setName(self::CONFIGURATION_CLASS_NAME);
        $data = file_get_contents($path);
        $success = Service::importClassDefinitionFromJson($class, $data);

    }

    public function canBeInstalled(): bool
    {
        return true;
    }

}
