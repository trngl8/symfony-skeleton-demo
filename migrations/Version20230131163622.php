<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131163622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_cards ADD price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE app_cards ADD price_sale DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE app_cards ADD quantity INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_cards ADD available BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_cards DROP price');
        $this->addSql('ALTER TABLE app_cards DROP price_sale');
        $this->addSql('ALTER TABLE app_cards DROP quantity');
        $this->addSql('ALTER TABLE app_cards DROP available');
    }
}
