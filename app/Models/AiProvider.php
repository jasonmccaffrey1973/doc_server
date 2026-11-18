<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    use HasUuids;

    protected $fillable = [
        'key',
        'name',
        'services',
        'default_base_url',
        'auth_type',
        'credential_fields',
        'credential_header',
        'api_key_url',
        'capabilities',
        'is_enabled',
    ];

    protected $casts = [
        'services' => 'array',
        'credential_fields' => 'array',
        'capabilities' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function connections(): HasMany
    {
        return $this->hasMany(AiProviderConnection::class);
    }

    public function getDefaultBaseUrlAttribute(): ?string
    {
        return $this->attributes['default_base_url'] ?? null;
    }

    public function getAuthTypeAttribute(): string
    {
        return (string) ($this->attributes['auth_type'] ?? 'api-key');
    }

    public function getCredentialFieldsAttribute(): array
    {
        $value = $this->attributes['credential_fields'] ?? null;
        if (is_array($value)) {
            return $value;
        }
        return is_string($value) ? (json_decode($value, true) ?: []) : [];
    }

    public function getCredentialHeaderAttribute(): ?string
    {
        return $this->attributes['credential_header'] ?? null;
    }

    public function getApiKeyUrlAttribute(): ?string
    {
        return $this->attributes['api_key_url'] ?? null;
    }

    public function getServicesAttribute(): array
    {
        $value = $this->attributes['services'] ?? null;
        if (is_array($value)) {
            return $value;
        }
        return is_string($value) ? (json_decode($value, true) ?: []) : [];
    }
}
