<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Services\RequirementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobFindController extends Controller
{
    /** @var string */
    protected const VALIDATION_RULES_COMPANY = 'required|array';

    /** @var string */
    protected const VALIDATION_RULES_REQUIREMENT = 'string';

    /** @var CompanyService */
    protected $companyService;

    /** @var RequirementService */
    protected $requirementService;

    public function __construct(CompanyService $companyService, RequirementService $requirementService)
    {
        $this->companyService = $companyService;
        $this->requirementService = $requirementService;
    }

    public function companies(Request $request): View
    {
        $this->validate($request, ['requirements' => self::VALIDATION_RULES_COMPANY]);

        return view('company-list', [
            'companies' => $this->companyService->companies($request->get('requirements')),
        ]);
    }

    public function requirements(Request $request): JsonResponse
    {
        $this->validate($request, ['queryString' => self::VALIDATION_RULES_REQUIREMENT]);

        return response()->json($this->requirementService->requirements($request->get('queryString')));
    }
}
