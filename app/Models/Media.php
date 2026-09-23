<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
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
        'filename',
        'original_name',
        'media_type',
        'mime_type',
        'size',
        'path',
        'alt_text',
        'storage_location_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'kind',
        'name',
        'mimeType',
        'altText',
        'createdAt',
    ];

    /**
     * Get the storage location that owns this media.
     */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class);
    }

    /**
     * Get the user that owns this media.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full URL to access this media.
     */
    public function getUrlAttribute(): string
    {
        return $this->storageLocation?->getMediaUrl($this->path) ?? '';
    }

    /**
     * Get the media kind (alias for media_type).
     */
    public function getKindAttribute(): string
    {
        return $this->attributes['media_type'] ?? '';
    }

    /**
     * Get the media name (alias for original_name).
     */
    public function getNameAttribute(): string
    {
        return $this->attributes['original_name'] ?? '';
    }

    /**
     * Get the MIME type.
     */
    public function getMimeTypeAttribute(): ?string
    {
        return $this->attributes['mime_type'] ?? null;
    }

    /**
     * Get the alt text.
     */
    public function getAltTextAttribute(): ?string
    {
        return $this->attributes['alt_text'] ?? null;
    }

    /**
     * Get the creation timestamp formatted as ISO string.
     */
    public function getCreatedAtAttribute(): string
    {
        return isset($this->attributes['created_at'])
            ? (string) $this->attributes['created_at']
            : '';
    }
}
