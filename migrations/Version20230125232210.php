<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230125232210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_users ADD is_verified BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER SEQUENCE profile_id_seq RENAME TO app_profiles_id_seq');
        $this->addSql('ALTER TABLE profile RENAME TO app_profiles');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_users DROP is_verified');
        $this->addSql('ALTER SEQUENCE app_profiles_id_seq RENAME TO profile_id_seq');
        $this->addSql('ALTER TABLE app_profiles RENAME TO profile');
    }
}
