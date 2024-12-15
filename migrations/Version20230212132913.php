<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212132913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_products ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_products ADD CONSTRAINT FK_BB3A3AF7727ACA70 FOREIGN KEY (parent_id) REFERENCES app_products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BB3A3AF7727ACA70 ON app_products (parent_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_products DROP CONSTRAINT FK_BB3A3AF7727ACA70');
        $this->addSql('DROP INDEX IDX_BB3A3AF7727ACA70');
        $this->addSql('ALTER TABLE app_products DROP parent_id');
    }
}
