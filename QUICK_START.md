# User Activity Tracking - Quick Start Guide

## ✅ Implementation Complete!

Your user activity tracking system is now fully implemented and ready to use.

## What's Working

### Automatic Tracking
- ✅ **Page Views**: Every authenticated user page visit is logged automatically
- ✅ **Login Events**: Captured when users log in
- ✅ **Logout Events**: Captured when users log out
- ✅ **Browser Metadata**: Browser type, version, OS, device type (Desktop/Mobile/Tablet)
- ✅ **IP Address**: User's IP address is recorded
- ✅ **Response Time**: Page load time in milliseconds

### Database
- ✅ Migration applied: `user_activities` table created
- ✅ Relationships: User model linked to activities
- ✅ Indexes: Optimized queries for common filters

## Usage Examples

### View a User's Activities
```php
$user = User::find($userId);
$activities = $user->activities()->latest()->get();

foreach ($activities as $activity) {
    echo "{$activity->activity_type} - {$activity->path} - {$activity->created_at}";
}
```

### Get Page Views Only
```php
$pageViews = UserActivity::pageViews()
    ->forUser($userId)
    ->latest()
    ->paginate(20);
```

### Get Recent Activities (Last Hour)
```php
$recentActivities = UserActivity::forUser($userId)
    ->recent(60) // 60 minutes
    ->latest()
    ->get();
```

### Get Login History
```php
$logins = UserActivity::logins()
    ->forUser($userId)
    ->orderByDesc('created_at')
    ->get();
```

### Access Browser Metadata
```php
$activity = UserActivity::first();
$metadata = $activity->metadata;

echo $metadata['browser']; // Chrome, Firefox, etc.
echo $metadata['os'];      // Windows, macOS, etc.
echo $metadata['device_type']; // Desktop, Mobile, Tablet
```

## Next Steps

### 1. Create GraphQL Query (Optional)
Add to your schema to expose activities via GraphQL:
```graphql
extend type Query {
  userActivities(userId: String!, limit: Int): [UserActivity!]! @paginate
}

type UserActivity {
  id: String!
  activity_type: String!
  path: String
  metadata: JSON
  created_at: DateTime!
}
```

### 2. Build Analytics Dashboard
- Page view trends
- Most visited pages
- User activity heatmap
- Login frequency
- Device/browser distribution

### 3. Add More Activity Types
Log custom activities:
```php
use App\Services\UserActivityLogger;

UserActivityLogger::log(
    user: $user,
    activityType: 'document_viewed',
    path: '/documents/123',
    metadata: ['document_type' => 'pdf', 'pages' => 10]
);
```

### 4. Setup Activity Retention Policy
Keep storage under control:
```php
// app/Console/Commands/CleanOldActivities.php
UserActivity::where('created_at', '<', now()->subMonths(3))->delete();
```

## Files Modified/Created

| File | Status |
|------|--------|
| Migration | ✅ Created & Applied |
| UserActivity Model | ✅ Created |
| User Model | ✅ Updated |
| UserActivityLogger Service | ✅ Created |
| TrackPageView Middleware | ✅ Created & Registered |
| LogUserLogin Listener | ✅ Created & Registered |
| LogUserLogout Listener | ✅ Created & Registered |
| UserActivityFactory | ✅ Created |
| Tests | ✅ Created |

## Troubleshooting

### Activities not being logged?
1. Check user is authenticated: `auth()->check()`
2. Verify middleware is registered in `bootstrap/app.php`
3. Check event listeners in `AppServiceProvider.php`

### Want to exclude certain paths?
Update the middleware in `app/Http/Middleware/TrackPageView.php`:
```php
if (in_array($request->path(), ['health', 'up'])) {
    return $next($request);
}
```

## Performance Notes
- Indexes are in place for efficient queries
- Page view logging happens after response (non-blocking)
- Consider archiving old records quarterly

## Documentation
See `ACTIVITY_TRACKING.md` for complete technical documentation.

---

**Ready to go!** Start tracking user activities now. 🚀
