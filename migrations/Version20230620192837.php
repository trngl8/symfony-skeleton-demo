<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230620192837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add time data table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE app_time_data_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_time_data (id INT NOT NULL, uuid UUID NOT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE app_time_data_id_seq CASCADE');
        $this->addSql('DROP TABLE app_time_data');
    }
}
