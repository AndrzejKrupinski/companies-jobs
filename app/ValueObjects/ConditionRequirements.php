<?php declare(strict_types = 1);

namespace App\ValueObjects;

use App\Models\Condition;

class ConditionRequirements
{
    /** @var int */
    protected $index;

    /** @var array */
    protected $requirementsOfCondition;

    /** @var array */
    protected $requirementsPerCompany;

    /** @var Condition */
    protected $condition;

    public function __construct(
        int $index,
        array $requirementsOfCondition,
        array $requirementsPerCompany,
        Condition $condition
    ) {
        $this->index = $index;
        $this->requirementsOfCondition = $requirementsOfCondition;
        $this->requirementsPerCompany = $requirementsPerCompany;
        $this->condition = $condition;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data[0],
            $data[1],
            $data[2],
            $data[3]
        );
    }

    public function index(): int
    {
        return $this->index;
    }

    public function requirementsOfCondition(): array
    {
        return $this->requirementsOfCondition;
    }

    public function requirementsPerCompany(): array
    {
        return $this->requirementsPerCompany;
    }

    public function condition(): Condition
    {
        return $this->condition;
    }
}
