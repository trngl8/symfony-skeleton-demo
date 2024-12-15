<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230223200418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add weight, country_code, period to card';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_cards ADD weight INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_cards ADD country_code VARCHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_cards ADD period VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_cards DROP weight');
        $this->addSql('ALTER TABLE app_cards DROP country_code');
        $this->addSql('ALTER TABLE app_cards DROP period');
    }
}
