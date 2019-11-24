<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Services\JobFindService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobFindController extends Controller
{
    /** @var string */
    protected const VALIDATION_RULES_COMPANY = 'required|array';

    /** @var string */
    protected const VALIDATION_RULES_REQUIREMENT = 'string';

    /** @var JobFindService */
    protected $service;

    public function __construct(JobFindService $jobFindService)
    {
        $this->service = $jobFindService;
    }

    public function companies(Request $request): View
    {
        $this->validate($request, ['requirements' => self::VALIDATION_RULES_COMPANY]);

        return view('company-list', [
            'companies' => $this->service->companies($request->get('requirements')),
        ]);
    }

    public function requirements(Request $request): JsonResponse
    {
        $this->validate($request, ['queryString' => self::VALIDATION_RULES_REQUIREMENT]);

        return response()->json($this->service->requirements($request->get('queryString')));
    }
}
