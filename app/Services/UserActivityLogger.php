<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;

class UserActivityLogger
{
    /**
     * Log a user activity.
     */
    public static function log(
        ?User $user,
        string $activityType,
        ?string $path = null,
        ?string $routeName = null,
        ?string $method = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?array $metadata = null,
        ?int $responseStatus = null,
        ?int $responseTimeMs = null
    ): UserActivity {
        if (! $user) {
            return new UserActivity;
        }

        return UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'path' => $path,
            'route_name' => $routeName,
            'method' => $method,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'metadata' => $metadata,
            'response_status' => $responseStatus,
            'response_time_ms' => $responseTimeMs,
        ]);
    }

    /**
     * Log a page view activity.
     */
    public static function logPageView(
        ?User $user,
        Request $request,
        ?int $responseStatus = null,
        ?int $responseTimeMs = null
    ): UserActivity {
        if (! $user) {
            return new UserActivity;
        }

        return self::log(
            user: $user,
            activityType: 'page_view',
            path: $request->path(),
            routeName: $request->route()?->getName(),
            method: $request->method(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            metadata: self::extractBrowserMetadata($request->userAgent()),
            responseStatus: $responseStatus,
            responseTimeMs: $responseTimeMs
        );
    }

    /**
     * Log a login activity.
     */
    public static function logLogin(
        User $user,
        Request $request
    ): UserActivity {
        return self::log(
            user: $user,
            activityType: 'login',
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            metadata: self::extractBrowserMetadata($request->userAgent())
        );
    }

    /**
     * Log a logout activity.
     */
    public static function logLogout(
        User $user,
        Request $request
    ): UserActivity {
        return self::log(
            user: $user,
            activityType: 'logout',
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );
    }

    /**
     * Extract browser and device metadata from user agent string.
     */
    private static function extractBrowserMetadata(?string $userAgent): ?array
    {
        if (! $userAgent) {
            return null;
        }

        $metadata = [
            'user_agent_string' => $userAgent,
        ];

        // Browser detection
        if (preg_match('/Chrome\/([0-9.]+)/', $userAgent, $match)) {
            $metadata['browser'] = 'Chrome';
            $metadata['browser_version'] = $match[1];
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent, $match)) {
            $metadata['browser'] = 'Firefox';
            $metadata['browser_version'] = $match[1];
        } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent, $match) && ! strpos($userAgent, 'Chrome')) {
            $metadata['browser'] = 'Safari';
            $metadata['browser_version'] = $match[1];
        } elseif (preg_match('/Edge\/([0-9.]+)/', $userAgent, $match)) {
            $metadata['browser'] = 'Edge';
            $metadata['browser_version'] = $match[1];
        }

        // OS detection
        if (strpos($userAgent, 'Windows')) {
            $metadata['os'] = 'Windows';
            if (preg_match('/Windows NT ([0-9.]+)/', $userAgent, $match)) {
                $metadata['os_version'] = $match[1];
            }
        } elseif (strpos($userAgent, 'Mac')) {
            $metadata['os'] = 'macOS';
        } elseif (strpos($userAgent, 'Linux')) {
            $metadata['os'] = 'Linux';
        } elseif (strpos($userAgent, 'iPhone') || strpos($userAgent, 'iPad')) {
            $metadata['os'] = 'iOS';
        } elseif (strpos($userAgent, 'Android')) {
            $metadata['os'] = 'Android';
        }

        // Device detection
        if (strpos($userAgent, 'Mobile') || strpos($userAgent, 'Android')) {
            $metadata['device_type'] = 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') || strpos($userAgent, 'iPad')) {
            $metadata['device_type'] = 'Tablet';
        } else {
            $metadata['device_type'] = 'Desktop';
        }

        return $metadata;
    }
}
