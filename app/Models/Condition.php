<?php declare(strict_types = 1);

namespace App\Models;

use App\Condition\Enums\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MadWeb\Enum\EnumCastable;

class Condition extends Model
{
    use EnumCastable;

    /** @var string */
    protected $table = 'conditions';

    /** @var array */
    protected $fillable = ['type', 'company_id'];

    protected $casts = [
        'type' => Type::class,
    ];

    public function id(): int
    {
        return $this->getKey();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requirements(): BelongsToMany
    {
        return $this->belongsToMany(Requirement::class, 'condition_requirements', 'condition_id', 'requirement_id');
    }
}
