<?php

namespace Tests\Feature;

use App\Models\Requirement;
use App\Repositories\CompanyRepository;
use App\Repositories\RequirementRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JobFindTest extends TestCase
{
    use DatabaseMigrations;

    /** @var CompanyRepository */
    protected $companyRepository;

    /** @var RequirementRepository */
    protected $requirementRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->companyRepository = $this->app->make(CompanyRepository::class);
        $this->requirementRepository = $this->app->make(RequirementRepository::class);
    }

    /**
     * @test
     */
    public function willGetRequirements(): void
    {
        $testRequirementTitle = 'TestRequirement';
        $requirement = $this->requirementRepository->save(['title' => $testRequirementTitle]);
        Cache::add('testRequirementId', $requirement->getKey(), 1);

        $response = $this->post('requirements', ['queryString' => $testRequirementTitle]);

        $response->assertStatus(200);
        $response->assertJson([$requirement->toArray()]);
    }

    /**
     * @test
     */
    public function willGetCompanies(): void
    {
        $testCompanyName = 'TestCompany';
        $company = $this->companyRepository->save(['name' => $testCompanyName]);
        $testRequirementId = Cache::get('testRequirementId', '1');
        DB::table('company_requirements')
            ->insert([
                'company_id' => $company->getKey(),
                'requirement_id' => $testRequirementId,
                'created_at' => Carbon::now(),
            ]);
        Cache::forget('testRequirementId');
        $company->fresh();

        $response = $this->post('companies', ['requirements' => [$testRequirementId]]);

        $response->assertStatus(200);
dd($this->getResponseData($response, 'companies'));
        // $response->assertJson([$requirement->toArray()]);
    }

    protected function getResponseData($response, $key)
    {
        return $response->getOriginalContent()->getData()[$key];
    }
}
