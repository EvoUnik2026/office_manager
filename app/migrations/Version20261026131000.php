<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261026131000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create company table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, registration_number VARCHAR(100) NOT NULL, name VARCHAR(150) NOT NULL, status VARCHAR(20) NOT NULL DEFAULT \'ACTIVE\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id), UNIQUE INDEX UNIQ_REGISTRATION_NUMBER (registration_number)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE company');
    }
}
