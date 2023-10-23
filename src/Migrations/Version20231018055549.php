<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018055549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the `products` table';
    }

    public function up(Schema $schema): void
    {
        // Create a new table named 'products'
        $productsTable = $schema->createTable('products');

        // Add columns to the 'products' table
        $productsTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $productsTable->addColumn('name', 'string');
        $productsTable->addColumn('description', 'text', ['notnull' => false]);

        // Set the primary key column
        $productsTable->setPrimaryKey(['id']);


    }

    public function down(Schema $schema): void
    {

        $schema->dropTable('products');
    }
}
