<?php

declare(strict_types=1);

namespace App\Domain\Office;

final class Company
{
    private readonly string $id;
    private CompanyStatus $status = CompanyStatus::Active;

    public function __construct(
        private readonly string $registrationNumber,
        private string $name,
    ) {
        if ('' === trim($registrationNumber)) {
            throw new \DomainException('Company registration number cannot be empty.');
        }

        if ('' === trim($name)) {
            throw new \DomainException('Company name cannot be empty.');
        }

        $this->name = trim($name);
        $this->id = self::newId();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function registrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function status(): CompanyStatus
    {
        return $this->status;
    }

    public function rename(string $name): void
    {
        if ('' === trim($name)) {
            throw new \DomainException('Company name cannot be empty.');
        }

        $this->name = trim($name);
    }

    /**
     * Archive the company only when prohibited active associations
     * have been ended.
     *
     * The application layer must determine the association state.
     */
    public function archive(bool $hasProhibitedActiveAssociations): void
    {
        if (CompanyStatus::Archived === $this->status) {
            throw new \DomainException('Company is already archived.');
        }

        if ($hasProhibitedActiveAssociations) {
            throw new \DomainException('Company cannot be archived while prohibited active associations remain.');
        }

        $this->status = CompanyStatus::Archived;
    }

    public function reactivate(): void
    {
        if (CompanyStatus::Active === $this->status) {
            throw new \DomainException('Company is already active.');
        }

        $this->status = CompanyStatus::Active;
    }

    private static function newId(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($data), 4),
        );
    }
}
