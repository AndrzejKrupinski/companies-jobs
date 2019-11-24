<?php declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Requirement;

class RequirementRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Requirement::class);
    }

    public function findByName(string $queryString): array
    {
        return $this->model::where('title', 'LIKE', "%$queryString%")
            ->get()
            ->toArray();
    }
}
