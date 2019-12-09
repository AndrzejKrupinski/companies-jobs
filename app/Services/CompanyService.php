<?php declare(strict_types = 1);

namespace App\Services;

use App\Repositories\CompanyRepository;
use App\Repositories\RequirementRepository;

class CompanyService
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
            $companies = $this->getCompaniesWithMatchingRequirements($requirementIds);
        }

        return $this->processCompanyResults($companies);
    }

    public function processCompanyResults(array $companies): array
    {
        $processedCompanies = [];

        foreach ($companies as $company) {
            $processedCompanies[$company->name] = $company->preparedRequirements();
        }

        return $processedCompanies;
    }

    protected function getCompaniesWithMatchingRequirements(array $requirementIds): array
    {
        $companiesToCheck = $this->companyRepository->findByRequirementIds($requirementIds);

        return $this->findCompaniesMatchingTheRequirements($companiesToCheck, $requirementIds);
    }

    protected function findCompaniesMatchingTheRequirements(array $companiesToCheck, array $requirementIds): array
    {
        $positiveVerifiedCompanies = [];

        foreach ($companiesToCheck as $companyToCheck) {
            if ($companyToCheck->isMatchingRequirements($requirementIds)) {
                $positiveVerifiedCompanies[] = $companyToCheck;
            }
        }

        return $positiveVerifiedCompanies;
    }
}
