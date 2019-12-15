<?php declare(strict_types = 1);

use App\Enums\Condition\Type;
use App\Repositories\CompanyRepository;
use App\Repositories\ConditionRepository;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    /** @var int */
    protected const MAX_NUMBER_OF_CONDITIONS_FOR_COMPANY = 3;

    /** @var ConditionRepository */
    protected $conditionRepository;

    public function __construct(ConditionRepository $conditionRepository)
    {
        $this->conditionRepository = $conditionRepository;
    }

    public function run(CompanyRepository $companyRepository): void
    {
        $this->assignConditionsToCompanies($companyRepository->all());
    }

    private function assignConditionsToCompanies(array $companies): void
    {
        foreach ($companies as $company) {
            $this->saveConditions(
                \rand(0, self::MAX_NUMBER_OF_CONDITIONS_FOR_COMPANY),
                $company->id()
            );
        }
    }

    private function saveConditions(int $numberOfConditionsToCreate, int $companyId): void
    {
        for ($i = 1; $i <= $numberOfConditionsToCreate; $i++) {
            $this->conditionRepository->save([
                'type' => Type::{Type::randomKey()}(),
                'company_id' => $companyId,
            ]);
        }
    }
}
