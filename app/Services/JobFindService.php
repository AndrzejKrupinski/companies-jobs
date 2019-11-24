<?php declare(strict_types = 1);

namespace App\Services;

use App\Models\Company;
use App\Repositories\CompanyRepository;
use App\Repositories\RequirementRepository;

class JobFindService
{
    /** @var CompanyRepository */
    protected $companyRepository;

    /** @var RequirementRepository */
    protected $requirementRepository;

    public function __construct(CompanyRepository $companyRepository, RequirementRepository $requirementRepository)
    {
        $this->companyRepository = $companyRepository;
        $this->requirementRepository = $requirementRepository;
    }

    public function companies(array $requirementIds = []): array
    {
        $companies = [];

        if ($requirementIds) {
            $companies = $this->processCompanyResults($this->getCompaniesWithMatchingRequirements($requirementIds));
        }

        return $companies;
    }

    public function requirements(string $queryString = ''): array
    {
        return $this->requirementRepository->findByName($queryString);
    }

    protected function getCompaniesWithMatchingRequirements(array $requirementIds): array
    {
        $companiesToCheck = $this->companyRepository->findByRequirementIds($requirementIds);

        return $this->findMatchingTheRequirements($companiesToCheck, $requirementIds);
    }

    protected function processCompanyResults(array $companies): array
    {
        $processedCompanies = [];

        foreach ($companies as $company) {
            $processedCompanies[$company->name] = $this->processRequirementResults($company);
        }

        return $processedCompanies;
    }

    protected function processRequirementResults(Company $company): array
    {
        $requirements = [];

        foreach ($company->requirements as $requirement) {
            $requirements[] = $requirement->title;
        }

        return $requirements;
    }

    protected function findMatchingTheRequirements(array $companiesToCheck, array $requirementIds): array
    {
        $positiveVerifiedCompanies = [];

        foreach ($companiesToCheck as $companyToCheck) {
            $requirementsOfCompanyToCheck = $companyToCheck->requirements;

            foreach ($requirementsOfCompanyToCheck as $index => $requirement) {
                if (!\in_array($requirement->getKey(), $requirementIds)) {
                    break;
                } elseif ($index === \count($requirementsOfCompanyToCheck) - 1) {
                    $positiveVerifiedCompanies[] = $companyToCheck;
                }
            }
        }

        return $positiveVerifiedCompanies;
    }
}
