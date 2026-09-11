<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageLocation extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'configuration',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'configuration' => 'json',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the media files stored in this location.
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Get the full URL for a media file stored in this location.
     */
    public function getMediaUrl(string $path): string
    {
        return match ($this->type) {
            'local' => asset('storage/' . $path),
            's3' => $this->configuration['bucket_url'] . '/' . $path,
            'gcs' => $this->configuration['bucket_url'] . '/' . $path,
            default => '',
        };
    }

    /**
     * Get the default storage location.
     */
    public static function getDefault(): ?static
    {
        return static::where('is_default', true)->first();
    }
}
