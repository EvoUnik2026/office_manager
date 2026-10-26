<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Company;
use App\Domain\Office\CompanyStatus;
use PHPUnit\Framework\TestCase;

final class CompanyTest extends TestCase
{
    public function testNewCompanyDefaultsToActive(): void
    {
        $company = new Company();

        self::assertSame(CompanyStatus::Active, $company->getStatus());
    }

    public function testStatusCanBeChanged(): void
    {
        $company = new Company();

        $company->setStatus(CompanyStatus::Archived);

        self::assertSame(CompanyStatus::Archived, $company->getStatus());
    }

    public function testRegistrationNumberCanBeSetAndRetrieved(): void
    {
        $company = new Company();

        $company->setRegistrationNumber('REG-001');

        self::assertSame('REG-001', $company->getRegistrationNumber());
    }

    public function testNameCanBeSetAndRetrieved(): void
    {
        $company = new Company();

        $company->setName('Example Company');

        self::assertSame('Example Company', $company->getName());
    }

    public function testCreatedAtAndUpdatedAtAreSetOnConstruction(): void
    {
        $company = new Company();

        self::assertNotNull($company->getCreatedAt());
        self::assertNotNull($company->getUpdatedAt());
    }

    public function testIdIsNullBeforePersisting(): void
    {
        $company = new Company();

        self::assertNull($company->getId());
    }
}
