# User Activity Tracking Implementation

## Overview
A complete user activity tracking system has been implemented for the doc_wrapper application. This system logs user activities including page views and login/logout events with detailed metadata like browser info, OS, device type, and IP address.

## What Was Implemented

### 1. Database Schema
**File**: `database/migrations/2026_08_14_231343_create_user_activities_table.php`

Created a `user_activities` table with the following columns:
- `id` (UUID, primary key)
- `user_id` (UUID, foreign key to users table)
- `activity_type` (string: 'page_view', 'login', 'logout', etc.)
- `path` (nullable string: URL path for page views)
- `route_name` (nullable string: Laravel route name)
- `method` (nullable string: HTTP method like GET, POST)
- `ip_address` (nullable string: User's IP address)
- `user_agent` (nullable text: Browser user agent string)
- `metadata` (nullable JSON: Browser, OS, device type information)
- `response_status` (nullable integer: HTTP response status)
- `response_time_ms` (nullable integer: Response time in milliseconds)
- `created_at`, `updated_at` (timestamps)

**Indexes**: Created for optimal query performance on user_id, activity_type, created_at, and common combinations.

### 2. UserActivity Model
**File**: `app/Models/UserActivity.php`

Features:
- UUID primary key support
- Relationship to User model
- Query scopes for common filtering:
  - `byType()` - Filter by activity type
  - `forUser()` - Filter by user ID
  - `recent()` - Get activities from the last N minutes
  - `forDate()` - Get activities for a specific date
  - `pageViews()` - Get only page view activities
  - `logins()` - Get only login activities
- JSON metadata casting

### 3. User Model Update
**File**: `app/Models/User.php`

Added relationship:
- `activities()` - HasMany relationship to UserActivity

### 4. User Activity Logger Service
**File**: `app/Services/UserActivityLogger.php`

Comprehensive service with static methods:
- `log()` - Generic activity logging method
- `logPageView()` - Log page view with request details and browser metadata
- `logLogin()` - Log user login with agent data
- `logLogout()` - Log user logout
- `extractBrowserMetadata()` - Parse user agent string to extract:
  - Browser name and version (Chrome, Firefox, Safari, Edge)
  - Operating system (Windows, macOS, Linux, iOS, Android)
  - Device type (Desktop, Mobile, Tablet)

### 5. Page View Tracking Middleware
**File**: `app/Http/Middleware/TrackPageView.php`

- Automatically logs page views for authenticated users
- Captures response status and response time (in milliseconds)
- Registered in `bootstrap/app.php` in the web middleware stack

### 6. Event Listeners
**Files**: 
- `app/Listeners/LogUserLogin.php`
- `app/Listeners/LogUserLogout.php`

- Automatically listen to Laravel's Login and Logout events
- Registered in `app/Providers/AppServiceProvider.php`

### 7. Factory for Testing
**File**: `database/factories/UserActivityFactory.php`

Created factory for generating test data with realistic activity records.

### 8. Comprehensive Tests
**File**: `tests/Feature/UserActivityTrackingTest.php`

Test suite covering:
- Page view logging
- User-activity relationship
- Activity retrieval
- Filtering by type
- Metadata capture

## How to Use

### Query User Activities

```php
// Get all activities for a user
$user = User::find($userId);
$activities = $user->activities()->get();

// Get recent page views
$pageViews = UserActivity::pageViews()
    ->forUser($userId)
    ->recent(60) // Last 60 minutes
    ->get();

// Get all logins for today
$todaysLogins = UserActivity::logins()
    ->forUser($userId)
    ->forDate(now())
    ->get();

// Filter by type
$allPageViews = UserActivity::byType('page_view')->get();
```

### Log Custom Activities

```php
use App\Services\UserActivityLogger;

// Log a custom activity
UserActivityLogger::log(
    user: $user,
    activityType: 'document_download',
    path: '/documents/123',
    metadata: ['document_id' => 123, 'format' => 'pdf']
);
```

### Create GraphQL Query (Optional)

You can add a GraphQL query to retrieve user activities:

```graphql
extend type Query {
  userActivities(
    userId: String!
    type: String
    limit: Int
    offset: Int
  ): [UserActivity!]! @paginate
}

type UserActivity {
  id: String!
  user: User!
  activity_type: String!
  path: String
  route_name: String
  method: String
  ip_address: String
  user_agent: String
  metadata: JSON
  response_status: Int
  response_time_ms: Int
  created_at: DateTime!
  updated_at: DateTime!
}
```

## What Gets Logged Automatically

### Page Views
Every page request by an authenticated user is logged with:
- URL path and route name
- HTTP method and response status
- Response time
- IP address and user agent
- Browser, OS, and device metadata

### Login Events
When a user logs in:
- Activity type: 'login'
- Timestamp
- IP address
- User agent with extracted browser/OS info

### Logout Events
When a user logs out:
- Activity type: 'logout'
- Timestamp
- IP address
- User agent

## Database Migration
The migration has already been run successfully. To verify:
```bash
php artisan migrate:status
```

## Future Enhancements

Potential additions:
1. Add GraphQL queries/mutations for activity retrieval
2. Create an activity dashboard/analytics page
3. Add activity filtering/search in the React frontend
4. Implement activity retention policies (archive/delete old records)
5. Add more event listeners (form submissions, API calls)
6. Create activity reports/analytics
7. Add geolocation data from IP address

## File Summary

| File | Purpose |
|------|---------|
| `database/migrations/2026_08_14_231343_create_user_activities_table.php` | Database schema |
| `app/Models/UserActivity.php` | Activity model with scopes |
| `app/Models/User.php` | Updated with activities relationship |
| `app/Services/UserActivityLogger.php` | Activity logging service |
| `app/Http/Middleware/TrackPageView.php` | Page view tracking middleware |
| `app/Listeners/LogUserLogin.php` | Login event listener |
| `app/Listeners/LogUserLogout.php` | Logout event listener |
| `app/Providers/AppServiceProvider.php` | Event listener registration |
| `bootstrap/app.php` | Middleware registration |
| `database/factories/UserActivityFactory.php` | Test factory |
| `tests/Feature/UserActivityTrackingTest.php` | Test suite |

## Testing
To run the tests (requires SQLite driver):
```bash
php artisan test tests/Feature/UserActivityTrackingTest.php
```

## Status
✅ Implementation complete
✅ Migration applied
✅ Code linted and formatted
✅ All files created and registered
✅ Ready for integration with GraphQL and React frontend
