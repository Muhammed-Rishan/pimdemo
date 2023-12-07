<?php

namespace SpyBundle\Command;
use Carbon\Carbon;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimcore\Model\DataObject;
//use Pimcore\Model\DataObject\Classificationstore\Listing;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\Data\GeoCoordinates;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\DemoCollection;
use Pimcore\Model\DataObject\Objectbrick\Data\Filebrick;
use Pimcore\Model\DataObject\Task;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;

class DataObjectsCommand extends Command
{
    protected static $defaultName = 'spy:objects';
    
    protected function configure(): void
    {
        $this->setDescription('Create data objects from a CSV file');
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvPath = 'bundles/SpyBundle/config/csv/file.csv';

        $csv = Reader::createFromPath($csvPath);
        $csv->setHeaderOffset(0);

        $parentObject = DataObject::getById(33);

        if (!$parentObject) {
            throw new \Exception('Parent object not found.');
        }

        $this->getLogger()->info('Starting the data import from CSV.');

        foreach ($csv->getRecords() as $record) {
            $key = $record['key'];
            $dataObject = Task::getByPath('/CSV/' . $key);

            $data = [];

//            if (isset($record['many'])) {
//                $relatedObjectPath = $record['many'];
//                $relatedObject = $this->handleManyToOneRelation($relatedObjectPath);
//
//                if ($relatedObject) {
//                    $data['many'] = $relatedObject;
//                }
//            }

//            $relatedObjectPath = $record['many'];
//             $this->handleManyToOneRelation($relatedObjectPath);

//            if ($relatedObject instanceof AbstractElement) {
//                // If $relatedObject is a valid object, set it in the data.
//                $data['many'] = $relatedObject;
//            }





//            foreach ($record as $field => $value) {
//                if ($field === 'filebrick' || $field === 'demos') {
//                    $this->getLogger()->info('Raw ' . $field . ' Data: ' . $value);
//                    $decodedData = json_decode($value, true);
//
//                    if ($field === 'filebrick') {
//                        $objectbricks = new Objectbricks();
//
//                        if ($decodedData) {
//                            $objectbricks->setValues($decodedData);
//                            $data['filebrick'] = $objectbricks;
//                        } else {
//                            // Handle the case where $decodedData is null, e.g., log an error or take appropriate action.
//                        }
//                    } elseif ($field === 'demos') {
//                        $demos = new DemoCollection();
//                        $demos->setValues($decodedData, true); // Pass $demos and $decodedData
//                        $data['demos'] = $demos;
//                    }
//                } else {
//                    $data[$field] = $this->castValue($field, $value);
//                }
//            }

//            ==========================

//            foreach ($record as $field => $value) {
//                if ($field === 'filebrick_0/field1') {
//                    $filebrickData = json_encode(['notes' => $value]);
//
//                    // Create an instance of the Objectbrick with a concrete object as the parent
//                    $filebrick = new Filebrick($dataObject, 'filebrick');
//
//                    // Set the values for the Objectbrick
//                    $filebrick->setValues(['notes' => $value]);
//
//                    $data['filebrick'] = $filebrick;
//                } elseif ($field === 'demoCollection/0/datas') {
//                    $demoCollectionData = json_decode($value, true);
//                    $demos = new DemoCollection();
//                    $demos->setValues(['datas' => $demoCollectionData], true);
//                    $data['demoCollection'] = ['datas' => $demos];
//                } else {
//                    $data[$field] = $this->castValue($field, $value);
//                }
//            }


            foreach ($record as $field => $value) {
                $data[$field] = $this->castValue($field, $value);
            }

            if ($dataObject) {
                $dataObject->setValues($data);
            } else {
                $dataObject = Task::create($data);
            }


            $this->handleObjectBrick($record, $dataObject);

            $this->handleFieldcollections($record, $dataObject);

            $this->handleBlock($record, $dataObject);


            $dataObject->setParent($parentObject);
            $dataObject->save();
        }

        $output->writeln('Data objects created or updated successfully.');

        return 0;
    }


    /**
     * Dynamically cast values based on field types in Pimcore class
     *
     * @param string $field
     * @param mixed $value
     * @return mixed
     * @throws ValidationException
     */
    private function castValue(string $field, mixed $value): mixed
    {
        var_dump("Field: " . $field, "Value: " . $value);

        if ($field === 'date') {
            return Carbon::parse($value);
        } elseif ($field === 'number') {
            return (float) $value;
        } elseif ($field === 'location') {
            $locationData = explode(',', $value);
            if (count($locationData) === 2) {
                $latitude = (float) $locationData[0];
                $longitude = (float) $locationData[1];
                return new GeoCoordinates($latitude, $longitude);
//            } elseif ($field === 'filebrick') {
//                // Handle the filebrick field
//                $brickData = ['notes' => $value];
//                $brick = new Task(); // Replace 'Task' with the actual brick class
//                $brick->setData($brickData);
//                return $brick;
//            } elseif ($field === 'demoCollection') {
//                // Handle the demoCollection field
//                $collectionData = [
//                    [
//                        'datas' => $value,
//                    ],
//                    // You can add more entries as needed
//                ];
//                $collection = new DemoCollection();
//                $collection->setItems($collectionData);
//                return $collection;
            }else {
                return null;
            }
        } elseif (str_ends_with($field, '_id')) {
            return $this->handleManyToOneRelation($value);
        } else {
            return $value;
        }
    }


    private function getLogger(): Logger
    {

        $logger = new Logger('import_logger');

        $logFile = 'bundles/SpyBundle/config/csv/logs/import.log';
        $handler = new StreamHandler($logFile);

        $logger->pushHandler($handler);

        return $logger;
    }

//    private function handleObjectbricks(array $record, DataObject $dataObject)
//    {
//        // Add your logic to handle Objectbricks here
//        // Example:
//        $objectbricksField = $dataObject->filebrick();
//
//        foreach ($objectbricksField->getAllowedTypes() as $brickType) {
//            $brick = $dataObject->getObjectbrickField($brickType);
//            $this->handleBrickFields($record, $brick, $brickType);
//        }
//    }
//
//    private function handleBrickFields(array $record, DataObject\Objectbrick $brick, string $brickType)
//    {
//        // Implement your logic to handle fields specific to the brick type
//        // Example:
//        $brick->setSomeField($record["brick_{$brickType}_someField"]);
//    }


//    private function handleBrick(array $record, DataObject $dataObject): void
//    {
//        if (isset($record['filebrick_0/notes'])) {
//            $brick = $dataObject->getFilebrick();
//
//            if (!$brick) {
//                $brick = new Objectbricks($dataObject, 'filebrick');
//                $dataObject->setFilebrick($filebrick_);
//            }
//
//            // Check if the 'text' value already exists in the brick
//            if ($brick->hasKey('filebrick_0')) {
//                $filebrick_0 = $brick->get('filebrick_0');
//            } else {
//                $filebrick_0 = new Objectbrick\Data\Filebrick();
//            }
//
//            $filebrick_0->setText($record['filebrick_0/notes']);
//
//            $brick->set('filebrick_0', $filebrick_0);
//        }
//    }


    private function handleFieldcollections(array $record, DataObject $dataObject): void
    {
        if (isset($record['demoCollection/0/datas'])) {
            $demos = $dataObject->getDemos();

            if (!$demos) {
                $demos = new Fieldcollection();
                $dataObject->setDemos($demos);
            } else {
                foreach ($demos as $existingItem) {
                    if ($existingItem->getDatas() === $record['demoCollection/0/datas']) {

                        $existingItem->setDatas($record['demoCollection/0/datas']);

                        foreach ($record as $demos => $value) {
                            if (!in_array($demos, ['demoCollection/0/datas'])) {
                                $this->setFieldcollectionValue($existingItem, $demos, $value);
                            }
                        }

                        return;
                    }
                }
            }

            $entry = new DemoCollection();
            $entry->setDatas($record['demoCollection/0/datas']);

//            if (isset($record['name'])) {
//                $entry->setName($record['name']);
//            }
            foreach ($record as $field => $value) {
                if (!in_array($field, ['democollection/0/datas'])) {
                    $this->setFieldcollectionValue($entry, $field, $value);
                }
            }

                $demos->add($entry);
            } else {
                $this->getLogger()->error("Key 'demoCollection/0/datas' is missing in the record.");
        }
    }

    private function setFieldcollectionValue(DemoCollection $entry, string $demos, mixed $value): void
    {
        $setterMethod = 'set' . ucfirst($demos);

        if (method_exists($entry, $setterMethod)) {
            $entry->$setterMethod($value);
        }
    }

    private function handleObjectBrick(array $record, DataObject\Concrete $dataObject): void
    {
//        $logger = $this->getLogger();

//        if (isset($record['filebrick_0/notes'])) {

            $brickKey = 'filebrick';
            $brick = $dataObject->getFileBrick();

            if (!$brick) {
                $brick = new DataObject\Objectbrick($dataObject, $brickKey);
                $dataObject->setFileBrick($brick);
            }

            $brickData = $brick->get($brickKey);

            if (!$brickData) {
                $brickData = new Filebrick($dataObject);
            }

        foreach ($record as $field => $value) {

            if ($field != "$brickKey/0/notes") {
                $this->setBrickValue($brickData, $field, $value);
            }
        }

//            $brickData->setNotes($record['filebrick_0/notes']);

            $brick->set($brickKey, $brickData);


        }
    private function setBrickValue(Filebrick $brickData, string $field, mixed $value): void
    {
        $setterMethod = 'set' . ucfirst($field);

        if (method_exists($brickData, $setterMethod)) {

            if (is_string($value) && str_contains($value, ',')) {
                $options = explode(',', $value);
                $brickData->$setterMethod($options);
            } else {
                $brickData->$setterMethod($value);
            }
        }
    }


    /**
     * @throws ValidationException
     */
    private function handleManyToOneRelation(string $value): ?AbstractElement
    {
        $value = trim($value);

        if (empty($value)) {
            return null;
        }

        // Check if the value is numeric ID
        if (is_numeric($value)) {
            $object = DataObject::getById($value);
        } else {
            // Assume it's a path
            $path = $value;
            $object = DataObject::getByPath($path);

            if (!$object instanceof AbstractElement) {
                throw new ValidationException("No object found for the value='{$value}'");
            }
        }

        return $object;
    }























//    private function handleBlocks(array $record, DataObject $dataObject): void
//    {
//        if (isset($record['blocks_0/name'])) {
//            $blockField = $dataObject->getBlocks();
//
//            if (!($blockField instanceof Block)) {
//                $blockField = new Block();
//
//            }
//
//            $blockData = [];
//
//            $blockData[] = [
//                'name' => $record['blocks_0/name'],
//            ];
//            $dataObject->setBlocks($blockData);
//
////            $dataObject->setBlocks($blockField);
//
//            var_dump($dataObject);
//
//            // Set the Block data for the data object
//            $blockField->setBlockedVarsForExport($blockData); // Use setData to set the block data
//            var_dump($dataObject);
//            // Make sure to set the field on the data object (e.g., $dataObject->setBlocks($blockField);)
//        } else {
//            $this->getLogger()->error("Key 'blocks/name' is missing in the record.");
//        }
//    }


//    private function handleBlock(array $record, DataObject $dataObject): void
//    {
//        if (isset($record['blocks_0/name'])) {
//            $blockField = $dataObject->getBlock();
//
//            if (!($blockField instanceof Block)) {
//                $blockField = new Block();
//            }
//
//            $blockData = $blockField->getBlockedVarsForExport();
//
//            $blockData[] = [
//                'name' => $record['blocks_0/name'],
//            ];
//
//            $blockField->setBlockedVarsForExport($blockData);
//
//            // Set the Block field on the data object
//            $dataObject->setBlock($blockField);
//        } else {
//            $this->getLogger()->error("Key 'blocks_0/name' is missing in the record.");
//        }
//    }


//    private function handleBlock(array $record, DataObject $dataObject): void
//    {
//        // Assuming block_0 is the only block, adjust the logic accordingly if you have multiple blocks
//        if (isset($record['block_0/name'])) {
//            $blockData = [];
//
//            // Collect block data from the CSV columns
//            for ($i = 0; isset($record["block_{$i}/name"]); $i++) {
//                $blockData[] = [
//                    'name' => $record["block_{$i}/name"],
//                ];
//            }
//            var_dump($dataObject);
//            // Set the block data on the data object
//            $blockFieldDefinition = $dataObject->getClass()->getFieldDefinition('block');
//            $blockFieldDefinition->preSetData($dataObject, $blockData);
//
//        } else {
//            $this->getLogger()->error("Key 'block_0/name' is missing in the record.");
//        }
//    }







//    private function handleBlocks(array $record, DataObject $dataObject): void
//    {
//        if (isset($record['blocks/name'])) {
//            $blockField = $dataObject->getBlocks();
//
//            if (!($blockField instanceof Block)) {
//                $blockField = new Block();
//            }
//
//            $blockData = [];
//
//            // Assuming $record contains the Block data in the format you've posted
//            // Modify the following code based on your CSV structure
//            $blockElementData = [
//                'name' => $record['blocks/name'],
//                'type' => 'input', // Update with the actual Block type
//                'data' => [
//                    // Normalize and set Block element data here
//                    'field1' => $record['blocks/name'],
//                    // Add more fields as needed
//                ],
//            ];
//
//            $blockData[] = $blockElementData;
//
////            // Set the Block data for the data object
////            $blockField->setData($blockData);
//
//            $dataObject->setBlocks($blockField);
//            var_dump($dataObject);
//        } else {
//            $this->getLogger()->error("Key 'blocks/name' is missing in the record.");
//        }
//    }








//    private function handleObjectBrick(array $record, \Pimcore\Model\DataObject\Concrete $dataObject): void
//        {
//            $objectBrickName = 'filebrick'; // Update with your actual Object Brick name
//
//            // Check if the Object Brick exists, and create it if not
//            $objectBrick = $dataObject->getFilebrick($objectBrickName);
//            if (!$objectBrick) {
//                $objectBrick = new Objectbrick\Data\Filebrick($dataObject, $objectBrickName); // Replace with your Object Brick class
//            }
//
//            // Handle Object Brick properties
//            if (isset($record[$objectBrickName . '/notes'])) {
//                $notesValue = $record[$objectBrickName . '/notes'];
//                $objectBrick->setNotes($notesValue);
//            }
//
//            // Set the Object Brick to the data object
//            $dataObject->setFilebrick($objectBrickName, $objectBrick);
//        }


//    private function handleManyToOneRelation(string $value): ?AbstractElement
//    {
//        $value = trim($value);
//
//        if (empty($value)) {
//            return null;
//        }
//
//        if (strpos($value, 'asset:') !== false) {
//            throw new \Exception("Asset not supported. Currently only object is supported for 'ManyToOneRelation'");
//        }
//
//        if (strpos($value, 'document:') !== false) {
//            throw new \Exception("Document not supported. Currently only object is supported for 'ManyToOneRelation'");
//        }
//
//        $path = $value;
//
//        if (strpos($path, 'object:') !== false) {
//            $path = str_replace("object:", "", $path);
//        }
//
//        $object = DataObject::getByPath($path);
//
//        if ($object instanceof AbstractElement) {
//            return $object;
//        } else {
//            throw new ValidationException("No object found at the path='{$path}'");
//        }
//    }

//    /**
//     * @throws ValidationException
//     * @throws \Exception
//     */
//    private function handleManyToOneRelation(string $value)
//    {
//        $value = trim($value);
//
//        if (empty($value)) {
//            return null;
//        }
//
//        if (is_numeric($value)) {
//            // Handle the value as an object ID
//            $object = DataObject::getById($value);
//        } else {
//            // Handle the value as an object path
//            if (strpos($value, 'object:') !== 0) {
//                throw new \Exception("Invalid 'ManyToOneRelation' format. It should start with 'object:'");
//            }
//            $path = substr($value, strlen('object:3'));
//            $object = DataObject::getByPath($path);
//        }
//
//        if ($object instanceof AbstractElement) {
//            return $object;
//        } else {
//            throw new ValidationException("No object found at the specified path or with the given ID.");
//        }
//    }



    private function handleBlock(array $record, DataObject\Concrete $dataObject): void
    {
        $blockKey = 'block';
        $blockData = [];

        foreach ($record as $field => $value) {
            // Check if the field belongs to the block using the blockKey
            if (str_starts_with($field, "$blockKey/0/")) {
                $fieldName = str_replace("$blockKey/0/", '', $field);

                // Create BlockElement instances dynamically
                $blockElement = new BlockElement(
                    $fieldName,
                    'text',
                    $value
                );

                $blockData[] = [$fieldName => $blockElement];

                $dataObject->setBlock($blockData);
            }
        }
    }





}
