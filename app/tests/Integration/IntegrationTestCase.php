<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class IntegrationTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    /**
     * @param array<string, mixed> $options
     */
    protected static function createKernel(array $options = []): Kernel
    {
        return new Kernel('test', true);
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        \assert($entityManager instanceof EntityManagerInterface);

        $this->entityManager = $entityManager;

        $connection = $entityManager->getConnection();

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

        $connection->executeStatement('DELETE FROM measurement');
        $connection->executeStatement('DELETE FROM sensor');

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
