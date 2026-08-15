<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $id
 * @property string $name
 * @property string|null $username
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'username', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Get the navigation links this user can access.
     *
     * @return array<int, array{name: string, path?: string, itemList?: array<int, array{name: string, path: string, itemList?: array<int, array{name: string, path: string}>}>}>
     */
    public function navigationLinks(): array
    {
        $navigation = [
            ['name' => 'Home', 'path' => '/', 'visits' => 0],
            ['name' => 'About', 'path' => '/about', 'visits' => 0],
            ['name' => 'Contact', 'path' => '/contact', 'visits' => 0],
            [
                'name' => 'Dashboard',
                'visits' => 0,
                'itemList' => [
                    ['name' => 'Analytics', 'path' => '/dashboard/analytics', 'visits' => 0],
                    ['name' => 'Reports', 'path' => '/dashboard/reports', 'visits' => 0],
                    ['name' => 'Settings', 'path' => '/dashboard/settings', 'visits' => 0],
                ],
            ],
            ['name' => 'Testing', 'path' => '/test', 'visits' => 0],
        ];

        return array_values(array_filter($navigation, fn (array $item): bool => $this->canAccessNavigationItem($item)));
    }

    /**
     * Determine whether the user can access a navigation item.
     *
     * @param  array{name: string, path?: string, itemList?: array<int, array{name: string, path: string}>}  $item
     */
    protected function canAccessNavigationItem(array $item): bool
    {
        return true;
    }

    /**
     * Get all activities for this user.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }
}
