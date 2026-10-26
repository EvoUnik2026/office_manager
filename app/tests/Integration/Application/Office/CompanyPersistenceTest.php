<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\Office;

use App\Entity\Company;
use App\Domain\Office\CompanyStatus;
use App\Repository\CompanyRepository;
use App\Tests\Integration\IntegrationTestCase;

final class CompanyPersistenceTest extends IntegrationTestCase
{
    public function testCompanyIsPersistedAndRetrievedFromMariaDb(): void
    {
        $company = (new Company())
            ->setRegistrationNumber('REG-001')
            ->setName('Example Company');

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        $companyId = $company->getId();
        self::assertNotNull($companyId);

        $this->entityManager->clear();

        $reloadedCompany = $this->entityManager->find(Company::class, $companyId);

        self::assertInstanceOf(Company::class, $reloadedCompany);
        self::assertSame('REG-001', $reloadedCompany->getRegistrationNumber());
        self::assertSame('Example Company', $reloadedCompany->getName());
        self::assertSame(CompanyStatus::Active, $reloadedCompany->getStatus());
    }

    public function testCompanyStatusIsPersistedAndRetrieved(): void
    {
        $company = (new Company())
            ->setRegistrationNumber('REG-002')
            ->setName('Archived Company')
            ->setStatus(CompanyStatus::Archived);

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        $companyId = $company->getId();
        self::assertNotNull($companyId);

        $this->entityManager->clear();

        $reloadedCompany = $this->entityManager->find(Company::class, $companyId);

        self::assertInstanceOf(Company::class, $reloadedCompany);

        self::assertSame(CompanyStatus::Archived, $reloadedCompany->getStatus());
    }

    public function testCompanyRepositoryCanFindByRegistrationNumber(): void
    {
        $company = (new Company())
            ->setRegistrationNumber('REG-003')
            ->setName('Repository Test Company');

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        $companyRepository = self::getContainer()->get(CompanyRepository::class);
        self::assertInstanceOf(CompanyRepository::class, $companyRepository);

        $found = $companyRepository->findOneBy(['registrationNumber' => 'REG-003']);

        self::assertNotNull($found);
        self::assertSame('REG-003', $found->getRegistrationNumber());
    }

    public function testDuplicateRegistrationNumberIsRejected(): void
    {
        $company1 = (new Company())
            ->setRegistrationNumber('REG-DUP')
            ->setName('First Company');
        $this->entityManager->persist($company1);
        $this->entityManager->flush();

        $company2 = (new Company())
            ->setRegistrationNumber('REG-DUP')
            ->setName('Second Company');

        $this->entityManager->persist($company2);

        $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
        $this->entityManager->flush();
    }
}
