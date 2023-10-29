<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231028150052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('actives');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('admin_user_id', 'integer');
        $table->addColumn('action', 'string', ['length' => 255]);
        $table->addColumn('timestamp', 'datetime');
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('actives');
    }
}
