<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /** @var string */
    protected $table = 'companies';

    /** @var array */
    protected $fillable = ['name',];

    public function id(): int
    {
        return $this->getKey();
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(Condition::class);
    }
}
