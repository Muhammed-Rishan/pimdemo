<?php

namespace SpyBundle\Command;
use Carbon\Carbon;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Data\GeoCoordinates;
use Pimcore\Model\DataObject\Fieldcollection\Data\DemoCollection;
use Pimcore\Model\DataObject\ClassDefinition\Data\Objectbricks;
use Pimcore\Model\DataObject\Task;
use Pimcore\Model\DataObject\Task\Brick;
use Pimcore\Model\DataObject\Task\Filebrick;
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
            } else {
                return null;
            }
        }  else {
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
}
