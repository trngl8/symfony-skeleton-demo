<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230126191402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE app_reset_passwords_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_reset_passwords (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_910D6449A76ED395 ON app_reset_passwords (user_id)');
        $this->addSql('COMMENT ON COLUMN app_reset_passwords.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN app_reset_passwords.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE app_reset_passwords ADD CONSTRAINT FK_910D6449A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE app_reset_passwords_id_seq CASCADE');
        $this->addSql('ALTER TABLE app_reset_passwords DROP CONSTRAINT FK_910D6449A76ED395');
        $this->addSql('DROP TABLE app_reset_passwords');
    }
}
