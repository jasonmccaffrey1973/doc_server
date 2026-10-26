<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiProviderConnection extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'ai_provider_id', 'label', 'credentials', 'settings', 'is_active',
    ];

    protected $hidden = ['credentials'];

    protected $appends = ['has_credentials'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function getHasCredentialsAttribute(): bool
    {
        return ! empty($this->credentials);
    }
}
