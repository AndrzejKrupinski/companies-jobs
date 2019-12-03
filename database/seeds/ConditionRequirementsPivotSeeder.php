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
            $randomNumber = \rand(1, $requirementsAmount);

            if (!\in_array($randomNumber, $requirementIds)
&& !\in_array($randomNumber, $this->requirementsPerCompanies[$companyId])) {
                $requirementIds[] = $randomNumber;
$this->updateRequirementsPerCompanies($companyId, $randomNumber);
            }
        }

        return $requirementIds;
    }

private function updateRequirementsPerCompanies(int $companyId, int $randomNumber): void
{
    if (isset($this->requirementsPerCompanies[$companyId])) {
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
}
