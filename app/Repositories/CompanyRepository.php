<?php declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Company::class);
    }

    public function findByRequirementIds(array $requirementIds): array
    {
        $conditionRequirements = DB::table('condition_requirements')
            ->join('conditions', 'condition_requirements.condition_id', '=', 'conditions.id')
            ->whereIn('requirement_id', $requirementIds)
            ->get()
            ->all();
        $companyIds = $this->getCompanyIds($conditionRequirements);

        return $this->model::find($companyIds)->all();
    }

    protected function getCompanyIds(array $conditionRequirements): array
    {
        $companyIds = [];

        foreach ($conditionRequirements as $companyRequirement) {
            $companyIds[] = $companyRequirement->company_id;
        }

        return $companyIds;
    }
}
