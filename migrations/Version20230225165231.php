<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230225165231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'filename (image) in products (categories)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_products ADD filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_products DROP filename');
    }
}
