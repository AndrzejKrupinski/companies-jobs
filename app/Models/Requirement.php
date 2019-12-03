<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Requirement extends Model
{
    /** @var string */
    protected $table = 'requirements';

    protected $fillable = ['title',];

    public function id(): int
    {
        return $this->getKey();
    }

    public function conditions(): BelongsToMany
    {
        return $this->belongsToMany(Condition::class, 'condition_requirements', 'requirement_id', 'condition_id');
    }
}
