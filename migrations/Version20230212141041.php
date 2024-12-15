<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212141041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_cards ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_cards ADD CONSTRAINT FK_D211D5304584665A FOREIGN KEY (product_id) REFERENCES app_products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_cards DROP CONSTRAINT FK_D211D5304584665A');
        $this->addSql('DROP INDEX IDX_D211D5304584665A');
        $this->addSql('ALTER TABLE app_cards DROP product_id');
    }
}
