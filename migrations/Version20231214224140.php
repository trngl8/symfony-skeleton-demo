<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231214224140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'upgrade schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE app_albums_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_blocks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_files_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_meetups_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_albums (id INT NOT NULL, title VARCHAR(255) NOT NULL, category VARCHAR(64) NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, public BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN app_albums.published_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE app_blocks (id INT NOT NULL, type VARCHAR(255) NOT NULL, route VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, controller VARCHAR(128) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE app_files (id INT NOT NULL, filename VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE app_meetups (id VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, planned_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, timezone VARCHAR(64) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE app_orders ALTER delivery_phone SET NOT NULL');
        $this->addSql('ALTER TABLE app_subscribes ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE app_subscribes ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE app_topics ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE app_users ALTER is_verified SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE app_albums_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_blocks_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_files_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_meetups_id_seq CASCADE');
        $this->addSql('DROP TABLE app_albums');
        $this->addSql('DROP TABLE app_blocks');
        $this->addSql('DROP TABLE app_files');
        $this->addSql('DROP TABLE app_meetups');
        $this->addSql('ALTER TABLE app_orders ALTER delivery_phone DROP NOT NULL');
        $this->addSql('ALTER TABLE app_topics ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE app_subscribes DROP email');
        $this->addSql('ALTER TABLE app_subscribes DROP name');
        $this->addSql('ALTER TABLE app_users ALTER is_verified DROP NOT NULL');
    }
}
