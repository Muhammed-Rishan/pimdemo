<?php
namespace SpyBundle\Tool;

use Exception;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Exception\DefinitionWriteException;


class Installer extends AbstractInstaller
{

    const CONFIGURATION_CLASS_NAME = 'Demo';

    public function install(): void
    {
        $filePath = __DIR__ . '/../../install/classes/class_definition.json';
        $path = realpath($filePath);
        $class = new ClassDefinition();
        $class->setName(self::CONFIGURATION_CLASS_NAME);
        $data = file_get_contents($path);
        $success = Service::importClassDefinitionFromJson($class, $data);

    }


    /**
     * @throws DefinitionWriteException
     * @throws Exception
     */
    public function uninstall(): void
    {
        $class = ClassDefinition::getByName(self::CONFIGURATION_CLASS_NAME);

        if ($class instanceof ClassDefinition) {
            $class->delete();
        }

    }

    /**
     * @throws Exception
     */
    public function canBeInstalled(): bool
    {
        $class = ClassDefinition::getByName(self::CONFIGURATION_CLASS_NAME);
        return !$class instanceof ClassDefinition;
    }

    /**
     * @throws Exception
     */
    public function canBeUninstalled(): bool
    {
        $class = ClassDefinition::getByName(self::CONFIGURATION_CLASS_NAME);
        return $class instanceof ClassDefinition;
    }
}
