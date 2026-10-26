<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Office;

use App\Domain\Office\Company;
use App\Domain\Office\CompanyStatus;
use PHPUnit\Framework\TestCase;

final class CompanyTest extends TestCase
{
    public function testNewCompanyIsActiveAndHasAnId(): void
    {
        $company = new Company('REG-001', 'Example Company');

        self::assertNotSame('', $company->id());
        self::assertSame('REG-001', $company->registrationNumber());
        self::assertSame('Example Company', $company->name());
        self::assertSame(CompanyStatus::Active, $company->status());
    }

    public function testCompanyNameCanBeChanged(): void
    {
        $company = new Company('REG-001', 'Old Name');

        $company->rename('New Name');

        self::assertSame('New Name', $company->name());
    }

    public function testCompanyNameIsTrimmed(): void
    {
        $company = new Company('REG-001', '  Example Company  ');

        self::assertSame('Example Company', $company->name());
    }

    public function testEmptyRegistrationNumberIsRejected(): void
    {
        $this->expectException(\DomainException::class);

        new Company('  ', 'Example Company');
    }

    public function testEmptyCompanyNameIsRejected(): void
    {
        $this->expectException(\DomainException::class);

        new Company('REG-001', '  ');
    }

    public function testCompanyCannotBeArchivedWhileProhibitedAssociationsRemain(): void
    {
        $company = new Company('REG-001', 'Example Company');

        $this->expectException(\DomainException::class);

        $company->archive(true);
    }

    public function testCompanyCanBeArchivedWithoutProhibitedAssociations(): void
    {
        $company = new Company('REG-001', 'Example Company');

        $company->archive(false);

        self::assertSame(CompanyStatus::Archived, $company->status());
    }

    public function testArchivedCompanyCanBeReactivated(): void
    {
        $company = new Company('REG-001', 'Example Company');
        $company->archive(false);

        $company->reactivate();

        self::assertSame(CompanyStatus::Active, $company->status());
    }

    public function testArchivingAnAlreadyArchivedCompanyIsRejected(): void
    {
        $company = new Company('REG-001', 'Example Company');
        $company->archive(false);

        $this->expectException(\DomainException::class);

        $company->archive(false);
    }

    public function testReactivatingAnActiveCompanyIsRejected(): void
    {
        $company = new Company('REG-001', 'Example Company');

        $this->expectException(\DomainException::class);

        $company->reactivate();
    }

    public function testRegistrationNumberDoesNotChangeWhenNameChanges(): void
    {
        $company = new Company('REG-001', 'Old Name');

        $company->rename('New Name');

        self::assertSame('REG-001', $company->registrationNumber());
    }
}
