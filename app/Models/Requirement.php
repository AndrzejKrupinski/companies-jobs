<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Requirement extends Model
{
    /** @var string */
    protected $table = 'requirements';

    protected $fillable = ['title',];

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_requirements', 'requirement_id', 'company_id');
    }
}
