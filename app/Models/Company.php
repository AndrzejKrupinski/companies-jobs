<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $table = 'companies';

    protected $fillable = ['name',];

    public function name(): string
    {
        return $this->name;
    }

    public function requirements(): BelongsToMany
    {
        return $this->belongsToMany(Requirement::class, 'company_requirements', 'company_id', 'requirement_id');
    }
}
