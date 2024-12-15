<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231214163042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'application jobs table creation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE app_jobs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_jobs (id INT NOT NULL, title VARCHAR(255) NOT NULL, crontab VARCHAR(64) DEFAULT NULL, PRIMARY KEY(id))');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE jobs_id_seq CASCADE');
        $this->addSql('DROP TABLE app_jobs');
    }
}
