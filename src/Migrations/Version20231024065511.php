<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231024065511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $votesTable = $schema->createTable('votes');
        $votesTable->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $votesTable->addColumn('username', Types::STRING, ['notnull' => false, 'length' => 255]);
        $votesTable->addColumn('score', Types::INTEGER, ['notnull' => false, 'length' => 5]);
        $votesTable->setPrimaryKey(['id']);
    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
