<?php declare(strict_types = 1);

namespace App\ValueObjects;

use Illuminate\Support\Collection;

class CompanyConditions
{
    /** @var string */
    protected $companyToCheckName;

    /** @var Collection */
    protected $conditionsOfCompanyToCheck;

    public function __construct(string $companyToCheckName, Collection $conditionsOfCompanyToCheck)
    {
        $this->companyToCheckName = $companyToCheckName;
        $this->conditionsOfCompanyToCheck = $conditionsOfCompanyToCheck;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data[0],
            $data[1]
        );
    }

    public function companyToCheckName(): string
    {
        return $this->companyToCheckName;
    }

    public function conditionsOfCompanyToCheck(): Collection
    {
        return $this->conditionsOfCompanyToCheck;
    }
}
