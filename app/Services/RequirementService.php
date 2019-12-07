<?php declare(strict_types = 1);

namespace App\Services;

use App\Repositories\RequirementRepository;

class RequirementService
{
    /** @var RequirementRepository */
    protected $requirementRepository;

    public function __construct(RequirementRepository $requirementRepository)
    {
        $this->requirementRepository = $requirementRepository;
    }

    public function requirements(string $queryString = ''): array
    {
        return $this->requirementRepository->findByName($queryString);
    }
}
