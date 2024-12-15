<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230704123608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'delivery_name in orders';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_orders ADD delivery_name VARCHAR(128) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_orders ALTER delivery_phone DROP NOT NULL');
    }
}
