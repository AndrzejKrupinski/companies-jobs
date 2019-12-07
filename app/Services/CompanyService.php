<?php declare(strict_types = 1);

namespace App\Services;

use App\Condition\Enums\Type;
use App\Models\Company;
use App\Models\Condition;
use App\Repositories\CompanyRepository;
use App\Repositories\RequirementRepository;
use App\ValueObjects\CompanyConditions;
use App\ValueObjects\ConditionRequirements;

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
                CompanyConditions::fromArray([
                    $companyToCheck->name,
                    $companyToCheck->conditions()->get(),
                ]),
                $requirementIds,
                $positiveVerifiedCompanies
            );
        }

        return $positiveVerifiedCompanies;
    }

    protected function checkConditionsForCompany(
        CompanyConditions $companyConditions,
        array $requirementIds,
        array &$positiveVerifiedCompanies
    ): array {
        $requirementsPerCompany = [];
        $conditionsOfCompanyToCheck = $companyConditions->conditionsOfCompanyToCheck();

        foreach ($conditionsOfCompanyToCheck as $index => $condition) {
            $conditionRequirements = $condition->requirements()->get()->pluck('id')->toArray();
            $verifiedConditionsAndRequirements = $this->verifyConditionByType(
                $companyConditions,
                ConditionRequirements::fromArray([
                    $index,
                    $conditionRequirements,
                    $requirementsPerCompany,
                    $condition,
                ]),
                $positiveVerifiedCompanies,
                $requirementIds,
                (int) $index
            );

            if (!$verifiedConditionsAndRequirements) {
                break;
            } else {
                [$requirementsPerCompany, $positiveVerifiedCompanies] = $verifiedConditionsAndRequirements;
            }
        }
dd($positiveVerifiedCompanies);
        return $positiveVerifiedCompanies;
    }

    protected function verifyConditionByType(
        CompanyConditions $companyConditions,
        ConditionRequirements $conditionRequirements,
        array $positiveVerifiedCompanies,
        array $requirementIds
    ): array {
        $condition = $conditionRequirements->condition();
        $requirementsOfConditions = $conditionRequirements->requirementsOfCondition();

        if ($condition->type->is(Type::ALTERNATIVE())) {
            [$requirementsPerCompany, $positiveVerifiedCompanies] = $this->checkCompletenessOfCondition(
                $companyConditions,
                $conditionRequirements,
                !\array_intersect($requirementsOfConditions, $requirementIds),
                $positiveVerifiedCompanies
            );
        } else {
            [$requirementsPerCompany, $positiveVerifiedCompanies] = $this->checkCompletenessOfCondition(
                $companyConditions,
                $conditionRequirements,
                !\in_array($condition->requirements()->get()[0]->id(), $requirementIds),
                $positiveVerifiedCompanies
            );
        }

        return [$requirementsPerCompany, $positiveVerifiedCompanies,];
    }

    protected function checkCompletenessOfCondition(
        CompanyConditions $companyConditions,
        ConditionRequirements $conditionRequirements,
        bool $statement,
        array $positiveVerifiedCompanies
    ): array {
        $requirementsPerCompany = $conditionRequirements->requirementsPerCompany();

        if ($statement) {
            return [$requirementsPerCompany, $positiveVerifiedCompanies,];
        } else {
            $requirementsPerCompany = $this->addRequirementPerCompany(
                $requirementsPerCompany,
                $conditionRequirements->requirementsOfCondition(),
                $conditionRequirements->condition()
            );

            $positiveVerifiedCompanies = $this->addPositiveVerifiedCompany(
                $companyConditions,
                $positiveVerifiedCompanies,
                $requirementsPerCompany,
                $conditionRequirements->index()
            );
        }

        return [$requirementsPerCompany, $positiveVerifiedCompanies,];
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

    protected function addPositiveVerifiedCompany(
        CompanyConditions $companyConditions,
        array &$positiveVerifiedCompanies,
        array $requirementsPerCompany,
        int $index
    ): array {
        $conditionsOfCompanyToCheck = $companyConditions->conditionsOfCompanyToCheck();
        $companyToCheckName = $companyConditions->companyToCheckName();

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
