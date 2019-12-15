<?php

namespace Tests\Feature;

use App\Enums\Condition\Type;
use App\Models\Condition;
use App\Models\Requirement;
use App\Repositories\CompanyRepository;
use App\Repositories\ConditionRepository;
use App\Repositories\RequirementRepository;
use App\Services\CompanyService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JobFindTest extends TestCase
{
    use DatabaseMigrations;

    /** @var CompanyService */
    protected $companyService;

    /** @var CompanyRepository */
    protected $companyRepository;

    /** @var ConditionRepository */
    protected $conditionRepository;

    /** @var RequirementRepository */
    protected $requirementRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->companyService = $this->app->make(CompanyService::class);
        $this->companyRepository = $this->app->make(CompanyRepository::class);
        $this->conditionRepository = $this->app->make(ConditionRepository::class);
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
        $company = $this->companyRepository->save(['name' => $this->getTestCompanyDataTitle()]);
        $conjunctiveCondition = $this->saveCondition(Type::CONJUNCTIVE(), $company->id());
        $alternativeCondition = $this->saveCondition(Type::ALTERNATIVE(), $company->id());;
        $conjunctiveRequirement = $this->saveRequirement($this->getTestConjunctiveRequirementTitle());
        $conjunctiveRequirementId = $conjunctiveRequirement->id();
        $alternativeRequirements = $this->getTestAlternativeRequirementTitles();
        $firstAlternativeRequirement = $this->saveRequirement($alternativeRequirements[0]);
        $secondAlternativeRequirement = $this->saveRequirement($alternativeRequirements[1]);
        $firstAlternativeRequirementId = $firstAlternativeRequirement->id();
        $secondAlternativeRequirementId = $secondAlternativeRequirement->id();;
        $this->saveConditionRequirementsPivotRecord($conjunctiveCondition->id(), $conjunctiveRequirementId);
        $this->saveConditionRequirementsPivotRecord($alternativeCondition->id(), $firstAlternativeRequirementId);
        $this->saveConditionRequirementsPivotRecord($alternativeCondition->id(), $secondAlternativeRequirementId);

        $firstResponse = $this->post(
            'companies',
            ['requirements' => [$conjunctiveRequirementId, $firstAlternativeRequirementId]]
        );
        $secondResponse = $this->post(
            'companies',
            ['requirements' => [$conjunctiveRequirementId, $firstAlternativeRequirementId]]
        );
        $thirdResponse = $this->post(
            'companies',
            ['requirements' => [$conjunctiveRequirementId]]
        );

        $firstResponse->assertStatus(200);
        $secondResponse->assertStatus(200);
        $thirdResponse->assertStatus(200);
        $this->assertSame(
            $this->getResponseData($firstResponse, 'companies'),
            $this->companyService->processCompanyResults([$company])
        );
        $this->assertSame(
            $this->getResponseData($secondResponse, 'companies'),
            $this->companyService->processCompanyResults([$company])
        );
        $this->assertNotSame(
            $this->getResponseData($thirdResponse, 'companies'),
            $this->companyService->processCompanyResults([$company])
        );
    }

    private function saveCondition(Type $type, int $companyId): Condition
    {
        return $this->conditionRepository->save([
            'type' => $type,
            'company_id' => $companyId,
        ]);
    }

    private function saveRequirement(string $testRequirementTitle): Requirement
    {
        return $this->requirementRepository->save(['title' => $testRequirementTitle]);
    }

    private function saveConditionRequirementsPivotRecord(int $conditionId, int $requirementId)
    {
        DB::table('condition_requirements')
            ->insert([
                'condition_id' => $conditionId,
                'requirement_id' => $requirementId,
                'created_at' => Carbon::now(),
            ]);
    }

    private function getResponseData($response, $key)
    {
        return $response->getOriginalContent()->getData()[$key];
    }

    private function getTestCompanyDataTitle(): string
    {
        return 'TestCompany';
    }

    private function getTestConjunctiveRequirementTitle(): string
    {
        return 'TestConjunctiveRequirement';
    }

    private function getTestAlternativeRequirementTitles(): array
    {
        return ['TestAlternativeRequirement1', 'TestAlternativeRequirement2'];
    }
}
