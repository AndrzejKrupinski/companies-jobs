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
        $companyRequirements = DB::table('company_requirements')
            ->whereIn('requirement_id', $requirementIds)
            ->get()
            ->all();

        $companyIds = $this->getCompanyIds($companyRequirements);

        return $this->model::find($companyIds)->all();
    }

    protected function getCompanyIds(array $companyRequirements): array
    {
        $companyIds = [];

        foreach ($companyRequirements as $companyRequirement) {
            $companyIds[] = $companyRequirement->company_id;
        }

        return $companyIds;
    }
}
