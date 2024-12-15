<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226075009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'product fees';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_products ADD fees INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_products DROP fees');
    }
}
