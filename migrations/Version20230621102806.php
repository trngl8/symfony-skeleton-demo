<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230621102806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'update time data table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_time_data ADD type VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_time_data ADD duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_time_data ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE app_time_data ALTER start_at DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN app_time_data.uuid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_time_data DROP type');
        $this->addSql('ALTER TABLE app_time_data DROP duration');
        $this->addSql('ALTER TABLE app_time_data ALTER uuid TYPE UUID');
        $this->addSql('ALTER TABLE app_time_data ALTER start_at SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN app_time_data.uuid IS NULL');
    }
}
