<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    use HasUuids;

    protected $fillable = ['key', 'name', 'capabilities', 'is_enabled'];

    protected $casts = [
        'capabilities' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function connections(): HasMany
    {
        return $this->hasMany(AiProviderConnection::class);
    }
}
