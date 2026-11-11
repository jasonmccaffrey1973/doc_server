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
        'service', 'base_url', 'auth_type', 'model_ids', 'status',
    ];

    protected $hidden = ['credentials'];

    protected $appends = ['has_credentials'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'model_ids' => 'array',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function getHasCredentialsAttribute(): bool
    {
        return ! empty($this->credentials);
    }

    public function getProviderIdAttribute(): string
    {
        return (string) $this->ai_provider_id;
    }

    public function getNameAttribute(): ?string
    {
        return $this->label ?: $this->provider?->name;
    }

    public function getCredentialKeysAttribute(): array
    {
        return array_keys($this->credentials ?? []);
    }

    public function getBaseUrlAttribute(): string
    {
        return (string) ($this->attributes['base_url'] ?? '');
    }

    public function getAuthTypeAttribute(): string
    {
        return (string) ($this->attributes['auth_type'] ?? 'api-key');
    }

    public function getModelIdsAttribute(): array
    {
        $value = $this->attributes['model_ids'] ?? null;
        return is_string($value) ? (json_decode($value, true) ?: []) : ($value ?: []);
    }

    public function getCreatedAtAttribute($value): ?\Illuminate\Support\Carbon
    {
        return $value ? $this->asDateTime($value) : null;
    }

    public function getUpdatedAtAttribute($value): ?\Illuminate\Support\Carbon
    {
        return $value ? $this->asDateTime($value) : null;
    }
}
