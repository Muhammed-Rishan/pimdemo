<?php


namespace SpyBundle\Command;

use Carbon\Carbon;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\Countrymultiselect;
use Pimcore\Model\DataObject\ClassDefinition\Data\Image;
use Pimcore\Model\DataObject\ClassDefinition\Data\Input;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyRelation;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation;
use Pimcore\Model\DataObject\ClassDefinition\Data\Multiselect;
use Pimcore\Model\DataObject\ClassDefinition\Data\Objectbricks;
use Pimcore\Model\DataObject\ClassDefinition\Data\Select;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Custom;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data\Textarea;
use Pimcore\Model\DataObject\ClassDefinition\Data\Wysiwyg;
use Pimcore\Model\DataObject\ClassDefinition\Data\Numeric;
use Pimcore\Model\DataObject\ClassDefinition\Data\Date;
use Pimcore\Model\DataObject\ClassDefinition\Data\Country;


class TextCommand extends Command
{
    protected static $defaultName = 'text:objects';

    protected function configure(): void
    {
        $this
            ->setDescription('An text custom command');
    }

    /**
     * @throws UnavailableStream
     * @throws ValidationException
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvPath = 'bundles/SpyBundle/config/csv/text.csv';

        if (!file_exists($csvPath)) {
            $output->writeln('CSV file does not exist.');

        }

        $csv = Reader::createFromPath($csvPath);
        $csv->setHeaderOffset(0);

        $parentObject = DataObject::getById(33);

        if (!$parentObject) {
            $output->writeln('Parent object not found.');
        }

        $this->getLogger()->info('Starting the data import from CSV.');

        foreach ($csv->getRecords() as $record) {
            $key = $record['key'];
            $dataObject = Custom::getByPath('/CSV/' . $key);

            if (!$dataObject) {
                $dataObject = new Custom();
            }

            $data = [];

//            foreach ($record as $field => $value) {
//                $fieldDefinition = $dataObject->getClass()->getFieldDefinition($field);
////                if ($field === 'country') {
////                    $fieldDefinition = $dataObject->getClass()->getFieldDefinition($field);
////                    $data[$field] = $this->handleCountry($value, $fieldDefinition);
////                }
//                if ($fieldDefinition instanceof Image) {
//                    $data[$field] = $this->handleImage($value);
//                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\ManyToOneRelation) {
//                    $data[$field] = $this->handleManyToOneRelation($value);
//                }elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\ManyToManyObjectRelation) {
//                    $data[$field] = $this->handleManyToManyObjectRelation($value);
//                }elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\ManyToManyRelation) {
//                    $data[$field] = $this->handleManyToManyRelation($value);
//                }
////                elseif ($field === 'active') {
////                    $data[$field] = $this->handleCheckbox($value);
////                }
//                else {
//                    $data[$field] = $this->castValue($field, $value);
//                }
//            }

            foreach ($record as $field => $value) {
                $fieldDefinition = $dataObject->getClass()->getFieldDefinition($field);

                if ($fieldDefinition instanceof Image) {
                    $data[$field] = $this->handleImage($value);
                } elseif ($fieldDefinition instanceof ManyToOneRelation) {
                    $data[$field] = $this->handleManyToOneRelation($value);
                } elseif ($fieldDefinition instanceof ManyToManyObjectRelation) {
                    $data[$field] = $this->handleManyToManyObjectRelation($value);
                } elseif ($fieldDefinition instanceof ManyToManyRelation) {
                    $data[$field] = $this->handleManyToManyRelation($value);
                }  elseif ($fieldDefinition instanceof Country) {
                    $data[$field] = $this->handleCountry($value);
                } elseif ($fieldDefinition instanceof CountryMultiSelect) {
                    $data[$field] = $this->handleCountryMultiSelect($value);
                } elseif ($fieldDefinition instanceof Objectbricks) {
                    $data[$field] = $this->handleBrick($value, $fieldDefinition, $dataObject);
                } elseif ($fieldDefinition instanceof Input) {
                    $data[$field] = $this->handleInput($value);
                } elseif ($fieldDefinition instanceof Textarea) {
                    $data[$field] = $this->handleTextarea($value);
                } elseif ($fieldDefinition instanceof Wysiwyg) {
                    $data[$field] = $this->handleWysiwyg($value);
                } elseif ($fieldDefinition instanceof Numeric) {
                    $data[$field] = $this->handleNumeric($value);
                } elseif ($fieldDefinition instanceof Date) {
                    $data[$field] = $this->handleDate($value);
                } elseif ($fieldDefinition instanceof Select) {
                    $data[$field] = $this->handleSelect($value);
                }  elseif ($fieldDefinition instanceof MultiSelect) {
                    $data[$field] = $this->handleMultiSelect($value);
                } else {
                    $data[$field] = $value;
                }
            }



            $dataObject->setValues($data);
            $dataObject->setKey($key);
            $dataObject->setParentId($parentObject->getId());
            $dataObject->save();
        }

        $this->getLogger()->info('Data import from CSV completed.');
        $output->writeln("Data objects are created or updated");

        return Command::SUCCESS;
    }

//    private function castValue(string $field, mixed $value): mixed
//    {
//        if ($field === 'text') {
//            return $value;
//        } elseif ($field === 'dob') {
//            return Carbon::parse($value);
//        } elseif ($field === 'age') {
//            return (float)$value;
//        }
//        elseif ($field === 'bool') {
//            return (bool)$value;
//        }
//        elseif ($field === 'active') {
//            return $this->handleCheckbox($value);
//        }
////        elseif ($field === 'country') {
////            return $value;
////        }
//        else {
//            return $value;
//        }
//    }

//    private function handleCountry(string $value): ?string
//    {
//        $value = trim($value);
//
//        if (empty($value)) {
//            return null;
//        }
//
//        return ucwords($value);
//    }
    private function handleInput(string $value): string
    {
        return trim($value);
    }

    private function handleTextarea(string $value): string
    {
        return trim($value);
    }

    private function handleWysiwyg(string $value): string
    {
        return trim($value);
    }

    private function handleNumeric(string $value): string
    {
        return $value;
    }

    private function handleSelect(string $value): string
    {
        return trim($value);
    }

    private function handleMultiSelect(string $value): array
    {
        $value = trim($value);

//        if (empty($value)) {
//            return;
//        }

        $elements = explode(",", $value);
        return array_map('trim', $elements);
    }

    private function handleCountry(string $value): string
    {
        $value = trim($value);

//        if (empty($value)) {
//            return;
//        }

        return ucwords($value);
    }
    private function handleCountryMultiSelect(string $value): array
    {
        $value = trim($value);

        if (empty($value)) {
            return [];
        }

        $countries = explode(",", $value);
        return array_map('trim', $countries);
    }


    /**
     * @throws ValidationException
     */
    private function handleDate(string $value): Carbon
    {
        $value = trim($value);

//        if (empty($value)) {
//            return;
//        }

        $carbon = Carbon::createFromFormat("Y-m-d", $value);

        if (!$carbon instanceof Carbon) {
            throw new ValidationException("Invalid datetime format given. It should be in the format 'Y-m-d' but given value is '$value'");
        }

        return $carbon;
    }


//    private function handleGender(string $value): ?string
//    {
//        return trim($value);
//    }


    /**
     */
    private function handleImage(string $value): ?Asset\Image
    {

        $path = trim($value);

        if (empty($path)) {
            return null;
        }

        $asset = Asset::getByPath($path);

        if (!$asset instanceof Asset\Image) {
            $this->getLogger()->error("No image found at the path '$value'");
        }

        return $asset;
    }


    /**
     * @throws ValidationException
     * @throws \Exception
     */
    private function handleManyToOneRelation($value): Asset
    {
        $value = trim($value);

        $path = $value;

        if (str_contains($value, 'asset:')) {
            // Handle asset
            $path = str_replace("asset:", "", $value);
        }

        if (str_contains($value, 'document:')) {
            throw new \Exception("Document not supported. Currently only object is supported for 'ManyToOneRelation'");
        }

        if (str_contains($path, 'object:')) {
            throw new \Exception("Document not supported. Currently only object is supported for 'ManyToOneRelation'");
        }

        $asset = Asset::getByPath($path);

        if (!$asset instanceof Asset) {
            throw new ValidationException("No object found at the path='{$path}'");
        }

        return $asset;
    }

    /**
     * @throws ValidationException
     */
    private function handleManyToManyObjectRelation(string $value): array
    {
        $value = trim($value);

        if (empty($value)) {
            return [];
        }

        $paths = explode(",", $value);
        $objects = [];

        foreach ($paths as $path) {
            $path = trim($path);
            $object = DataObject::getByPath($path);

            if (!$object instanceof DataObject\AbstractObject) {
                throw new ValidationException("No object found at the path='{$path}'");
            }

            $objects[] = $object;
        }

        return $objects;
    }

    /**
     * @throws ValidationException
     */
    private function handleManyToManyRelation(string $value): array
    {
        $value = trim($value);

        if (empty($value)) {
            return [];
        }

        $paths = explode(",", $value);
        $objects = [];

        foreach ($paths as $path) {
            $path = trim($path);
            $asset = Asset::getByPath($path);

            if (!$asset instanceof Asset) {
                throw new ValidationException("No asset found at the path='{$path}'");
            }

            $objects[] = $asset;
        }

        return $objects;
    }

    private function handleCheckbox(string $value): bool
    {
        $value = trim($value);
        return (bool)$value;
    }

    private function handleBrick(array $item, Objectbricks $field, Concrete $dataObject)
    {
        $getterField = "get" . ucfirst($field->getName());
        $objectbrick = $dataObject->{$getterField}();

        $brickName = $field->getAllowedTypes()[0]; // TODO: Loop for all allowed types if necessary.

        $getterBrick = "get" . ucfirst($brickName);
        $brick = $objectbrick->{$getterBrick}();

        if (!$brick instanceof Custom) {
            $brick = new Custom($dataObject);
            $setterBrick = "set" . ucfirst($brickName);
            $objectbrick->{$setterBrick}($brick);
        }

        $fields = $brick->getDefinition()->getFieldDefinitions();

        foreach ($fields as $fd) {
            if ($fd->getNotEditable()) {
                continue;
            }

            $fieldKey = $brickName . "~" . $fd->getName();

            if (array_key_exists($fieldKey, $item)) {
                $oldValue = (string) $item[$fieldKey];
                $setter = "set" . ucfirst($fd->getName());

                switch ($fd->getFieldtype()) {
                    case "input":
                        $newValue = $this->handleInput($oldValue, $fd);
                        break;

                    case "textarea":
                        $newValue = $this->handleTextarea($oldValue, $fd);
                        break;

                    case "wysiwyg":
                        $newValue = $this->handleWysiwyg($oldValue, $fd);
                        break;

                    case "numeric":
                        $newValue = $this->handleNumeric($oldValue, $fd);
                        break;

                    case "date":
                        $newValue = $this->handleDate($oldValue, $fd);
                        break;

                    // Add more cases for other field types as needed

                    default:
                        throw new \Exception("Data type '{$fd->getFieldtype()}' not supported for localized fields in the brick.");
                        break;
                }

                $brick->{$setter}($newValue);
            }
        }

        return $objectbrick;
    }




    private function getLogger(): Logger
    {
        $logger = new Logger('import_logger');
        $logFile = 'bundles/SpyBundle/config/csv/logs/import.log';
        $handler = new StreamHandler($logFile);

        $logger->pushHandler($handler);

        return $logger;
    }

}
