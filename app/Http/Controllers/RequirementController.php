<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Services\RequirementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RequirementController extends Controller
{
    /** @var string */
    protected const VALIDATION_RULES = 'string';

    /** @var RequirementService */
    protected $service;

    public function __construct(RequirementService $jobFindService)
    {
        $this->service = $jobFindService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->validate($request, ['queryString' => self::VALIDATION_RULES]);

        return response()->json($this->service->requirements($request->get('queryString')));
    }
}
