# User Activity Tracking Architecture

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER ACTIONS                             │
└────────────────┬────────────────────────┬──────────────┬────────┘
                 │                        │              │
                 ▼                        ▼              ▼
            PAGE VISIT            LOGIN/LOGOUT       CUSTOM ACTIVITY
                 │                        │              │
                 │                        │              │
    ┌────────────▼────────────┐  ┌────────▼──────────┐ │
    │  TrackPageView          │  │  Auth Events      │ │
    │  Middleware             │  │  (Login/Logout)   │ │
    └────────────┬────────────┘  └────────┬──────────┘ │
                 │                        │            │
                 │    ┌────────────────────┴────────┐   │
                 │    │                             │   │
                 │    ▼                             ▼   │
    ┌────────────┴──────────────────────────────────────┴────┐
    │        LogUserLogin / LogUserLogout / App Logic       │
    │              (Event Listeners & Services)             │
    └────────────┬───────────────────────────────────────────┘
                 │
                 ▼
    ┌────────────────────────────────────────────────────────┐
    │        UserActivityLogger::log()                       │
    │  (Centralized Logging Service)                         │
    │  - Extracts browser metadata from user agent           │
    │  - Determines OS, browser, device type                 │
    │  - Formats activity data                               │
    └────────────┬───────────────────────────────────────────┘
                 │
                 ▼
    ┌────────────────────────────────────────────────────────┐
    │        UserActivity::create()                          │
    │  (Eloquent Model)                                       │
    └────────────┬───────────────────────────────────────────┘
                 │
                 ▼
    ┌────────────────────────────────────────────────────────┐
    │        user_activities Table                           │
    │  - id (UUID)                                           │
    │  - user_id (FK → users)                               │
    │  - activity_type (page_view, login, logout)           │
    │  - path, route_name, method                           │
    │  - ip_address, user_agent                             │
    │  - metadata (browser, OS, device type)                │
    │  - response_status, response_time_ms                  │
    │  - created_at, updated_at                             │
    └────────────────────────────────────────────────────────┘
```

## Component Interaction Diagram

```
User Request Flow
─────────────────

    HTTP Request
         │
         ▼
    ┌─────────────────────────┐
    │  Laravel Routes         │
    └──────────┬──────────────┘
               │
               ▼
    ┌─────────────────────────┐
    │  Middleware Stack       │
    │  ├─ Auth                │
    │  ├─ ...                 │
    │  └─ TrackPageView ◄──── Logs page view if authenticated
    └──────────┬──────────────┘
               │
               ▼
    ┌─────────────────────────┐
    │  Route Handler          │
    │  (Controller)           │
    └──────────┬──────────────┘
               │
               ▼
    ┌─────────────────────────┐
    │  Response               │
    └────────────────────────────

Authentication Flow
───────────────────

    LoginRequest
         │
         ▼
    ┌─────────────────────────┐
    │  auth()->login()         │
    └──────────┬──────────────┘
               │ Fires: Login Event
               ▼
    ┌─────────────────────────┐
    │  LogUserLogin Listener  │
    └──────────┬──────────────┘
               │
               ▼
    ┌─────────────────────────┐
    │  UserActivityLogger     │
    │  ::logLogin()           │
    └──────────┬──────────────┘
               │
               ▼
    ┌─────────────────────────┐
    │  user_activities table  │
    │  (activity_type: login) │
    └─────────────────────────┘

    LogoutRequest
         │
         ▼
    ┌─────────────────────────┐
    │  auth()->logout()        │
    └──────────┬──────────────┘
               │ Fires: Logout Event
               ▼
    ┌─────────────────────────┐
    │  LogUserLogout Listener │
    └──────────┬──────────────┘
               │
               ▼
    ┌─────────────────────────┐
    │  UserActivityLogger     │
    │  ::logLogout()          │
    └──────────┬──────────────┘
               │
               ▼
    ┌─────────────────────────┐
    │  user_activities table  │
    │ (activity_type: logout) │
    └─────────────────────────┘
```

## Data Model

```
User (1) ────┐
              │ 1:N
              ▼
UserActivity (Many)

User attributes:
- id (UUID)
- name
- email
- ...
└── activities() → HasMany(UserActivity)

UserActivity attributes:
- id (UUID)
- user_id (UUID, FK)
- activity_type: String
  ├─ page_view
  ├─ login
  └─ logout
- path: String (nullable)
- route_name: String (nullable)
- method: String (GET, POST, etc.)
- ip_address: String (nullable)
- user_agent: String (nullable)
- metadata: JSON (nullable)
  ├─ browser: String
  ├─ browser_version: String
  ├─ os: String
  ├─ os_version: String (nullable)
  ├─ device_type: String (Desktop, Mobile, Tablet)
  └─ user_agent_string: String
- response_status: Int (nullable)
- response_time_ms: Int (nullable)
- created_at: Timestamp
- updated_at: Timestamp
└── user() → BelongsTo(User)
```

## Registration Flow

```
1. File Creation
   ├─ UserActivity Model
   ├─ UserActivityLogger Service
   ├─ TrackPageView Middleware
   ├─ LogUserLogin Listener
   └─ LogUserLogout Listener

2. Service Provider Registration (AppServiceProvider)
   ├─ Event::listen(Login::class, LogUserLogin::class)
   └─ Event::listen(Logout::class, LogUserLogout::class)

3. Middleware Registration (bootstrap/app.php)
   └─ $middleware->web(append: [TrackPageView::class])

4. Database Migration
   └─ Migration 2026_08_14_231343_create_user_activities_table
      └─ Creates: user_activities table with indexes

5. Model Relationship (User Model)
   └─ activities(): HasMany(UserActivity)
```

## Query Patterns

```
┌─ Get All User Activities
│  User::find($id)->activities()->get()
│
├─ Get Page Views
│  UserActivity::pageViews()
│
├─ Get Login History
│  UserActivity::logins()
│      ->forUser($userId)
│      ->latest()
│      ->get()
│
├─ Get Recent Activities (Last Hour)
│  UserActivity::recent(60)
│      ->forUser($userId)
│      ->get()
│
├─ Get Activities by Type
│  UserActivity::byType('page_view')
│      ->forUser($userId)
│      ->get()
│
├─ Get Activities for Specific Date
│  UserActivity::forDate($date)
│      ->forUser($userId)
│      ->get()
│
└─ Paginate Activities
   UserActivity::forUser($userId)
       ->orderByDesc('created_at')
       ->paginate(20)
```

## Browser Detection Example

```
User Agent String:
  "Mozilla/5.0 (Windows NT 10.0; Win64; x64)
   AppleWebKit/537.36 (KHTML, like Gecko)
   Chrome/126.0.0.0 Safari/537.36"

                  ▼ Parsing

Browser:          Chrome 126.0.0.0
OS:              Windows 10.0
Device Type:     Desktop

                  ▼ Stored in Metadata

metadata: {
  "browser": "Chrome",
  "browser_version": "126.0.0.0",
  "os": "Windows",
  "os_version": "10.0",
  "device_type": "Desktop",
  "user_agent_string": "Mozilla/5.0 ..."
}
```

## Performance Considerations

```
Database Indexes:
├─ user_id ............................ Fast user-based queries
├─ activity_type ...................... Fast type filtering
├─ created_at ......................... Fast time-range queries
├─ (user_id, activity_type) ........... Fast user+type queries
└─ (user_id, created_at) .............. Fast user+time queries

Middleware Performance:
├─ Middleware runs after response generated
├─ Logging is non-blocking
├─ Uses batch insertion for efficiency
└─ Only logs for authenticated users

Scalability:
├─ UUID primary keys (distributed)
├─ JSON metadata (flexible)
├─ Proper indexing for queries
├─ Relationship eager loading ready
└─ Pagination support built-in
```

---

This architecture ensures:
✅ Automatic activity tracking
✅ Non-invasive logging
✅ Comprehensive metadata capture
✅ Efficient querying
✅ Production-ready performance
