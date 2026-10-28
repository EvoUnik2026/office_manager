<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260911101439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'No-op: the sensor device_id unique index is created by Version20260909101908.';
    }

    public function up(Schema $schema): void
    {
        // Intentionally empty.
        // Version20260909101908 already creates the unique index
        // UNIQ_BC8617B094A4C7D4 on sensor.device_id.
    }

    public function down(Schema $schema): void
    {
        // Intentionally empty.
        // The index belongs to Version20260909101908.
        // Dropping it here would leave the earlier migration applied
        // while removing the index it owns.
    }
}