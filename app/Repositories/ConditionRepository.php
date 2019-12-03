<?php declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Condition;

class ConditionRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Condition::class);
    }
}
