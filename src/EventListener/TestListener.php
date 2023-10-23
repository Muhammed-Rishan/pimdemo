<?php

namespace App\EventListener;

use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DocumentEvent;


class TestListener
{
    private ApplicationLogger $logger;

    public function __construct(ApplicationLogger $logger)
    {
        $this->logger = $logger;
    }

    public function onPreUpdate(ElementEventInterface $e): void
    {
        if ($e instanceof AssetEvent) {

            $asset = $e->getAsset();

            if ($asset->getFileSize() > 1024 * 1024) {
                $this->logger->info('Asset with size greater than 1MB was updated.');
            }
        } else if ($e instanceof DocumentEvent) {
            $document = $e->getDocument();
            $this->logger->info('Document was updated.');
        } else if ($e instanceof DataObjectEvent) {
            // Handle object updates
            $object = $e->getObject();
            $this->logger->info('Data Object was updated.');


            // You can modify the object properties if needed.
//            $object->setMyValue(microtime(true));
            // No need to call save here; it's a pre-update event.
        }
    }

    public function onPreAdd(ElementEventInterface $e): void
    {
        if ($e instanceof AssetEvent) {
            $asset = $e->getAsset();
            $this->logger->info('Asset is being added.');


            if ($asset->getType() === 'image') {
                // Perform specific actions for images
            }
        } elseif ($e instanceof DocumentEvent) {
            $document = $e->getDocument();
            $this->logger->info('Document is being added.');


            if ($document->getPublished()) {
                // Perform actions for published documents
            }
        } elseif ($e instanceof DataObjectEvent) {
            $object = $e->getObject();
            $this->logger->info('Data Object is being added.');

            // You can add conditions based on the data object's properties or state
//            if ($object->getMyProperty() === 'some_value') {
//                // Perform specific actions for data objects with a certain property value
//            }
        }
    }


    public function onPreDelete(ElementEventInterface $e): void
    {
        if ($e instanceof AssetEvent) {
            $asset = $e->getAsset();
            $this->logger->info('Asset is being deleted: ' . $asset->getId());
        } elseif ($e instanceof DocumentEvent) {
            $document = $e->getDocument();
            $this->logger->info('Document is being deleted: ' . $document->getId());
        } elseif ($e instanceof DataObjectEvent) {
            $object = $e->getObject();
            $this->logger->info('Data Object is being deleted: ' . $object->getId());
        }
    }
}
