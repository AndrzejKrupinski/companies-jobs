<?php

use Illuminate\Database\Seeder;

use App\Condition\Enums\Type;
use App\Models\Condition;
use App\Repositories\ConditionRepository;
use App\Repositories\RequirementRepository;
use Illuminate\Support\Facades\DB;

class ConditionRequirementsPivotSeeder extends Seeder
{
    /** @var int */
    protected const MAXIMUM_REQUIREMENTS_FOR_ONE_ALTERNATIVE = 3;

    /** @var array */
    protected $requirementsPerCompanies;

    public function run(ConditionRepository $conditionRepository, RequirementRepository $requirementRepository): void
    {
        $this->assignRequirementsToConditions($conditionRepository->all(), $requirementRepository->all());
    }

    private function assignRequirementsToConditions(array $conditions, array $requirements): void
    {
        foreach ($conditions as $condition) {
            $requirementsAmountForCondition = $this->getNumberOfRequirementsForCondition($condition);

            $requirementIds = $this->getRandomRequirementIds(
                \count($requirements),
                $requirementsAmountForCondition,
                $condition->company_id
            );

            foreach ($requirementIds as $requirementId) {
                $this->insertRecord($requirementId, $condition->id());
            }
        }
    }

    private function getNumberOfRequirementsForCondition(Condition $condition): int
    {
        if ($condition->type->is(Type::ALTERNATIVE())) {
            $requirementsAmountForCondition = \rand(2, self::MAXIMUM_REQUIREMENTS_FOR_ONE_ALTERNATIVE);
        } else {
            $requirementsAmountForCondition = 1;
        }

        return $requirementsAmountForCondition;
    }

    private function getRandomRequirementIds(
        int $requirementsAmount,
        int $requirementsAmountForCondition,
        int $companyId
    ): array {
        $requirementIds = [];

        for ($i = 1; $i <= $requirementsAmountForCondition; $i++) {
            $requirementIds = $this->getRandomNumberForCompany(
                $requirementsAmount,
                $requirementsAmountForCondition,
                $companyId,
                $requirementIds
            );
        }

        return $requirementIds;
    }

    private function getRandomNumberForCompany(
        int $requirementsAmount,
        int $requirementsAmountForCondition,
        int $companyId,
        array $requirementIds
    ): array {
        $randomNumber = $this->generateRandomNumber($requirementsAmount);
        $presenceInRequirementsPerCompanies = isset($this->requirementsPerCompanies[$companyId]);
        $randomNumberIsNotUsedForCompany = !$presenceInRequirementsPerCompanies
            || ($presenceInRequirementsPerCompanies
            && !\in_array($randomNumber, $this->requirementsPerCompanies[$companyId]));

        if ($randomNumberIsNotUsedForCompany) {
            $requirementIds[] = $randomNumber;
            $this->updateRequirementsPerCompanies($presenceInRequirementsPerCompanies, $companyId, $randomNumber);
        } else {
            $requirementIds = $this->getRandomNumberForCompany(
                $requirementsAmount,
                $requirementsAmountForCondition,
                $companyId,
                $requirementIds
            );
        }

        return $requirementIds;
    }

    private function updateRequirementsPerCompanies(
        bool $presenceInRequirementsPerCompanies,
        int $companyId,
        int $randomNumber
    ): void {
        if ($presenceInRequirementsPerCompanies) {
            $this->requirementsPerCompanies[$companyId][] = $randomNumber;
        } else {
            $this->requirementsPerCompanies[$companyId] = [$randomNumber];
        }
    }

    private function insertRecord(int $requirementId, int $id): void
    {
        DB::insert(
            'INSERT INTO condition_requirements (requirement_id, condition_id) VALUES (?, ?)',
            [$requirementId, $id]
        );
    }

    private function generateRandomNumber(int $requirementsAmount): int
    {
        return \rand(1, $requirementsAmount);
    }
}
