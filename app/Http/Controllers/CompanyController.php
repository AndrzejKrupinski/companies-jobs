<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /** @var string */
    protected const VALIDATION_RULES = 'required|array';

    /** @var CompanyService */
    protected $service;

    public function __construct(CompanyService $jobFindService)
    {
        $this->service = $jobFindService;
    }

    public function show(Request $request): View
    {
        $this->validate($request, ['requirements' => self::VALIDATION_RULES]);

        return view('company-list', [
            'companies' => $this->service->companies($request->get('requirements')),
        ]);
    }
}
