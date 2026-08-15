<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $user_id
 * @property string $activity_type
 * @property string|null $path
 * @property string|null $route_name
 * @property string|null $method
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array|null $metadata
 * @property int|null $response_status
 * @property int|null $response_time_ms
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 */
#[Fillable(['user_id', 'activity_type', 'path', 'route_name', 'method', 'ip_address', 'user_agent', 'metadata', 'response_status', 'response_time_ms'])]
class UserActivity extends Model
{
    use HasUuids;

    protected $table = 'user_activities';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'json',
            'response_status' => 'integer',
            'response_time_ms' => 'integer',
        ];
    }

    /**
     * Get the user that owns this activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter activities by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope to filter activities by user.
     */
    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope to get activities for a specific date.
     */
    public function scopeForDate($query, Carbon $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope to get page view activities.
     */
    public function scopePageViews($query)
    {
        return $query->where('activity_type', 'page_view');
    }

    /**
     * Scope to get login activities.
     */
    public function scopeLogins($query)
    {
        return $query->where('activity_type', 'login');
    }
}
