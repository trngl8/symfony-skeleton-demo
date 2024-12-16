<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231217122620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'attachments table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE attachments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE attachments (id INT NOT NULL, filename VARCHAR(255) NOT NULL, dir VARCHAR(255) NOT NULL, size INT DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE attachments_id_seq CASCADE');
        $this->addSql('DROP TABLE attachments');
    }
}
