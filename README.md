# doc_wrapper

`doc_wrapper` is a Laravel 13 application with Inertia, React, Sanctum, Fortify, Lighthouse GraphQL, and GraphiQL. It provides a web dashboard, GraphQL auth flows, settings pages, and activity tracking for authenticated user actions.

## Tech Stack

- Laravel 13
- PHP 8.3
- React 19 + Inertia
- TypeScript
- Sanctum for API tokens
- Fortify for web authentication
- Lighthouse for GraphQL
- GraphiQL for API exploration
- Vite, Tailwind CSS, ESLint, Prettier, Pint, PHPStan, Pest

## Features

- Web authentication and profile/settings pages
- GraphQL login, logout, and me queries
- Sanctum token auth for API requests
- User activity tracking for login, logout, and page views
- User session metadata capture, including IP address, user agent, and browser details
- Navigation analytics data collection for source and destination paths

## Project Structure

- `app/` - application code, models, controllers, GraphQL resolvers, middleware, and providers
- `bootstrap/` - application bootstrap files
- `config/` - framework and package configuration
- `database/` - migrations, factories, and seeders
- `graphql/` - GraphQL schema and examples
- `resources/` - React, CSS, and frontend assets
- `routes/` - web and settings routes
- `tests/` - Pest feature and unit tests

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js 20+ recommended
- A database supported by Laravel

## Setup

Install dependencies:

```bash
composer install
npm install
```

Copy the environment file and generate the app key:

```bash
cp .env.example .env
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate
```

Build frontend assets:

```bash
npm run build
```

## Development

Run the app locally:

```bash
composer run dev
```

That starts the Laravel server, queue listener, and Vite dev server together.

## Useful Scripts

```bash
composer run lint
composer run lint:check
composer run types:check
composer run ci:check
composer run test
```

Frontend scripts:

```bash
npm run dev
npm run build
npm run lint
npm run lint:check
npm run format
npm run format:check
npm run types:check
```

## Authentication

The app uses two auth flows:

- Web auth via Fortify
- API auth via Sanctum bearer tokens

GraphQL login and logout are available through the schema in `graphql/schema.graphql`.

## Activity Tracking

This repository includes user activity tracking documentation and implementation files for recording authenticated user activity. The tracking system is centered around the `user_activities` table and supports:

- page view logging
- login and logout events
- request metadata capture
- browser and device metadata
- query helpers for activity history

See the following docs for more detail:

- `README_ACTIVITY_TRACKING.md`
- `QUICK_START.md`
- `ACTIVITY_TRACKING.md`
- `IMPLEMENTATION_SUMMARY.md`
- `ARCHITECTURE.md`

## GraphQL

The GraphQL schema is defined in `graphql/schema.graphql`. Common operations include:

- `login`
- `logout`
- `me`
- `navigation`

LMS authoring operations now available include:

- Queries: `courses`, `course`, `lessons`, `lesson`, `courseLessons`, `chapterLessons`
- Mutations: `createCourse`, `updateCourse`, `deleteCourse`
- Mutations: `createChapter`, `updateChapter`, `deleteChapter`
- Mutations: `createLesson`, `updateLesson`, `deleteLesson`
- Mutations: `addLessonToCourse`, `updateCourseLesson`
- Mutations: `placeCourseLessonInChapter`, `removeCourseLessonFromChapter`

Media management operations now available include:

- Mutations: `uploadMedia`, `bulkUploadMedia`, `deleteMedia`, `configureStorageLocation`
- Supports local filesystem, AWS S3, and Google Cloud Storage
- Media types: image, video, audio

Open GraphiQL to explore the API during development.

For copy/paste request examples and workflow order, see `graphql/graphiql-examples.md`.

## Testing

Run the test suite with:

```bash
php artisan test
```

The project also includes Pest, PHPStan, and frontend lint/type checks to keep the codebase consistent.

## Environment Notes

Typical Laravel environment variables apply. Common ones include:

- `APP_NAME`
- `APP_ENV`
- `APP_KEY`
- `APP_URL`
- `DB_CONNECTION`
- `DB_DATABASE`
- `SANCTUM_STATEFUL_DOMAINS`

## License

MIT