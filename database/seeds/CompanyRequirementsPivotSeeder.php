<?php

use Illuminate\Database\Seeder;

use App\Repositories\CompanyRepository;
use App\Repositories\RequirementRepository;
use Illuminate\Support\Facades\DB;

class CompanyRequirementsPivotSeeder extends Seeder
{
    public function run(CompanyRepository $companyRepository, RequirementRepository $requirementRepository): void
    {
        $this->assignRequirementsToCompanies($companyRepository->all(), $requirementRepository->all());
    }

    private function assignRequirementsToCompanies(array $companies, array $requirements): void
    {
        foreach ($companies as $company) {
            $requirementIds = $this->getRandomRequirementIds(\count($requirements));

            foreach ($requirementIds as $requirementId) {
                $this->insertRecord($requirementId, $company->getKey());
            }
        }
    }

    private function getRandomRequirementIds(int $requirementsAmount): array
    {
        $numberOfRequirementsForCompany = \rand(0, 3);
        $requirementIds = [];

        for ($i = 0; $i <= $numberOfRequirementsForCompany; $i++) {
            $randomNumber = \rand(1, $requirementsAmount);

            if (!\in_array($randomNumber, $requirementIds)) {
                $requirementIds[] = $randomNumber;
            }
        }

        return $requirementIds;
    }

    private function insertRecord(int $requirementId, int $id): void
    {
        DB::insert(
            'INSERT INTO company_requirements (requirement_id, company_id) VALUES (?, ?)',
            [$requirementId, $id]
        );
    }
}
