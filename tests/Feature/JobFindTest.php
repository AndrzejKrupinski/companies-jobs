<?php

namespace Tests\Feature;

use App\Repositories\CompanyRepository;
use App\Repositories\RequirementRepository;
use App\Services\JobFindService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JobFindTest extends TestCase
{
    use DatabaseMigrations;

    /** @var JobFindService */
    protected $jobFindService;

    /** @var CompanyRepository */
    protected $companyRepository;

    /** @var RequirementRepository */
    protected $requirementRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->jobFindService = $this->app->make(JobFindService::class);
        $this->companyRepository = $this->app->make(CompanyRepository::class);
        $this->requirementRepository = $this->app->make(RequirementRepository::class);
    }

    /**
     * @test
     */
    public function willGetRequirements(): void
    {
        $testRequirementTitle = $this->getTestCompanyDataTitle();
        $requirement = $this->requirementRepository->save(['title' => $testRequirementTitle]);

        $response = $this->post('requirements', ['queryString' => $testRequirementTitle]);

        $response->assertStatus(200);
        $response->assertJson([$requirement->toArray()]);
    }

    /**
     * @test
     */
    public function willGetCompanies(): void
    {
        $testRequirementTitle = $this->getTestRequirementDataTitle();
        $company = $this->companyRepository->save(['name' => $this->getTestCompanyDataTitle()]);
        $requirement = $this->requirementRepository->save(['title' => $testRequirementTitle]);
        $requirementId = $requirement->getKey();
        DB::table('company_requirements')
            ->insert([
                'company_id' => $company->getKey(),
                'requirement_id' => $requirementId,
                'created_at' => Carbon::now(),
            ]);

        $response = $this->post('companies', ['requirements' => [$requirementId]]);

        $response->assertStatus(200);
        $this->assertSame(
            $this->getResponseData($response, 'companies'),
            $this->jobFindService->processCompanyResults([$company])
        );
    }

    protected function getResponseData($response, $key)
    {
        return $response->getOriginalContent()->getData()[$key];
    }

    protected function getTestCompanyDataTitle(): string
    {
        return 'TestCompany';
    }

    protected function getTestRequirementDataTitle(): string
    {
        return 'TestRequirement';
    }
}
