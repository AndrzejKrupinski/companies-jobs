<?php declare(strict_types = 1);

namespace App\Services;

use App\Condition\Enums\Type;
use App\Models\Company;
use App\Models\Condition;
use App\Repositories\CompanyRepository;
use App\Repositories\RequirementRepository;
use Illuminate\Support\Collection;

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

        return $companies;
    }

    public function processCompanyResults(array $companies): array
    {
        $processedCompanies = [];

        foreach ($companies as $company) {
            $processedCompanies[$company->name] = $this->processRequirementResults($company);
        }

        return $processedCompanies;
    }

    protected function getCompaniesWithMatchingRequirements(array $requirementIds): array
    {
        $companiesToCheck = $this->companyRepository->findByRequirementIds($requirementIds);

        return $this->findCompaniesMatchingTheRequirements($companiesToCheck, $requirementIds);
    }

    protected function processRequirementResults(Company $company): array
    {
        $requirements = [];

        foreach ($company->conditions()->get() as $condition) {

            foreach ($condition->requirements()->get() as $requirement) {
                $requirements[] = $requirement->title;
            }
        }

        return $requirements;
    }

    protected function findCompaniesMatchingTheRequirements(array $companiesToCheck, array $requirementIds): array
    {
        $positiveVerifiedCompanies = [];

        foreach ($companiesToCheck as $companyToCheck) {
            $positiveVerifiedCompanies = $this->checkConditionsForCompany(
                $companyToCheck->conditions()->get(),
                $requirementIds,
                $positiveVerifiedCompanies,
                $companyToCheck->name
            );
        }

        return $positiveVerifiedCompanies;
    }

    // @todo - simplify/split this method (there is code doubled):
    protected function checkConditionsForCompany(
        Collection $conditionsOfCompanyToCheck,
        array $requirementIds,
        array &$positiveVerifiedCompanies,
        string $companyToCheckName
    ): array {
        $requirementsPerCompany = [];

        foreach ($conditionsOfCompanyToCheck as $index => $condition) {
            $conditionRequirements = $condition->requirements()->get()->pluck('id')->toArray();

            if ($condition->type->is(Type::ALTERNATIVE())) {
                if (!\array_intersect($conditionRequirements, $requirementIds)) {
                    break;
                } else {
                    $requirementsPerCompany = $this->addRequirementPerCompany(
                        $requirementsPerCompany,
                        $conditionRequirements,
                        $condition
                    );

                    $positiveVerifiedCompanies = $this->addPossitiveVerifiedCompany(
                        $positiveVerifiedCompanies,
                        $conditionsOfCompanyToCheck,
                        $requirementsPerCompany,
                        $companyToCheckName,
                        (int) $index
                    );
                }
            } else {
                if (!\in_array($condition->requirements()->get()[0]->id(), $requirementIds)) {
                    break;
                } else {
                    $requirementsPerCompany = $this->addRequirementPerCompany(
                        $requirementsPerCompany,
                        $conditionRequirements,
                        $condition
                    );

                    $positiveVerifiedCompanies = $this->addPossitiveVerifiedCompany(
                        $positiveVerifiedCompanies,
                        $conditionsOfCompanyToCheck,
                        $requirementsPerCompany,
                        $companyToCheckName,
                        (int) $index
                    );
                }
            }
        }

        return $positiveVerifiedCompanies;
    }

    protected function addRequirementPerCompany(
        array $requirementsPerCompany,
        array $conditionRequirements,
        Condition $condition
    ): array {
        $key = $condition->id() . '-' . $condition->type;
        $requirementsPerCompany[$key] = $this->prepareRequirements($conditionRequirements);

        return $requirementsPerCompany;
    }

    protected function addPossitiveVerifiedCompany(
        array &$positiveVerifiedCompanies,
        Collection $conditionsOfCompanyToCheck,
        array $requirementsPerCompany,
        string $companyToCheckName,
        int $index
    ): array {
        if ($index === \count($conditionsOfCompanyToCheck) - 1) {
            $positiveVerifiedCompanies[$companyToCheckName] = $requirementsPerCompany;
        }

        return $positiveVerifiedCompanies;
    }

    protected function prepareRequirements(array $requirementIds): array
    {
        $requirementTitles = [];

        foreach ($requirementIds as $requirementId) {
            $requirementTitles[] = $this->requirementRepository->find($requirementId)->title;
        }

        return $requirementTitles;
    }
}
