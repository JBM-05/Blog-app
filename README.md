# Blog App API (Laravel)

## Business scenario (what this app does)

This project is a simple blogging backend API:

- Users can sign up, log in, and update their profile (including an avatar).
- Authenticated users can create, list, update, and delete blog posts.
- Posts belong to categories.
- Users can comment on posts (supports threaded replies via `parent_id`).
- When a new post is published, other users receive an in-app notification (stored in the database) delivered via a queued job.

## Resources created (how requirements are fulfilled)

- **Auth**: Token-based auth via Laravel Sanctum.
- **Users**: `users` table + `User` model (roles: `user` default; `admin` for category admin actions).
- **Categories**: `categories` table + `Category` model; admin-only create/delete.
- **Posts**: `posts` table + `Post` model; supports optional featured image upload.
- **Comments**: `comments` table + `Comment` model; polymorphic `commentable_type/commentable_id` plus `parent_id` for replies.
- **Notifications**: `notifications` table; new post notifications stored using Laravel’s database notification channel.
- **Queue**: `jobs` table; new post notifications are dispatched/processed asynchronously.

## Tech stack / libraries used

- **Laravel 12**: application framework.
- **Laravel Sanctum** (`laravel/sanctum`): API authentication using personal access tokens.
- **Cloudinary PHP SDK** (`cloudinary/cloudinary_php`): image upload/delete for avatars and post images.
- **Database queue + database notifications**: built-in Laravel drivers, chosen so the app can run without Redis/SQS in local/dev.

## Setup

### Prerequisites

- PHP **8.2+**
- Composer
- PostgreSQL (recommended / used in this project)



### 1) Install dependencies

```bash
composer install
```

### 2) Create `.env`

If you don’t have one yet:

```bash
copy .env.example .env
php artisan key:generate
```

### 3) Configure environment variables

Recommended local config (PostgreSQL):

```env
APP_NAME="Blog App"
APP_ENV=local
APP_KEY=base64:...generated...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=blog_app
DB_USERNAME=postgres
DB_PASSWORD=your_password

QUEUE_CONNECTION=database

# Cloudinary (recommended if you use avatar/post images)
# Option A (most common):
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME

# Sanctum (only needed for SPA cookie auth; safe defaults are already set)
# FRONTEND_URL=http://localhost:5173
# SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,127.0.0.1:8000
```

# Sanctum (only needed for SPA cookie auth; safe defaults are already set)
# FRONTEND_URL=http://localhost:5173
# SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,127.0.0.1:8000
```

Notes:

- If Cloudinary is not configured and you call endpoints that upload images, those requests will fail.
- Notifications are stored in the database (no email config required).

### 4) Create the database

#### PostgreSQL

Create the database (choose one approach):

- Using `psql`:

```bash
psql -U postgres -c "CREATE DATABASE blog_app;"
```

- Or using `createdb`:

```bash
createdb -U postgres blog_app
```


## Running migrations, seeders, and tests

### Migrations

Run all migrations:

```bash
php artisan migrate
```

### Seeders

Run the default seeder:

```bash
php artisan db:seed
```

Or reset and reseed in one command:

```bash
php artisan migrate:fresh --seed
```

Seeder behavior:

- `DatabaseSeeder` creates a single user: `test@example.com`.

## Running the app locally

Start the API:

```bash
php artisan serve
```

Because new post notifications are queued, run a worker in another terminal:

```bash
php artisan queue:work
```

## Authentication model

This API uses Laravel Sanctum personal access tokens.

- On **signup/login**, the server sets an `api_token` cookie.
- Middleware `CookieTokenToHeader` converts the cookie into an `Authorization: Bearer ...` header when the header is missing.

For API clients (including Postman), you can:

1. Use the cookie automatically, or
2. Extract the token and send it as `Authorization: Bearer <token>`.

## API endpoints (from `routes/api.php`)

Base URL (local): `http://127.0.0.1:8000`

### Auth

- `POST /api/signup` (JSON: `name`, `email`, `password`)
- `POST /api/login` (JSON: `email`, `password`)
- `POST /api/logout` (auth)
- `GET /api/check-auth` (auth)
- `PUT /api/updateProfile` (auth, multipart: optional `name`, `email`, `password`, `avatar` file)

### Categories

- `GET /api/getCategories` (auth)
- `POST /api/addCategory` (auth + admin, JSON: `name`)
- `DELETE /api/deleteCategory` (auth + admin, JSON: `name`)

### Posts

- `POST /api/createPost` (auth, multipart: `title`, `content`, `category_id`, optional `image` file)
- `GET /api/getPosts` (auth, query: optional `category_id`, `user_id`) - paginated
- `PUT /api/posts/{post}` (auth, multipart: optional `title`, `content`, `category_id`, optional `image` file)
- `DELETE /api/posts/{post}` (auth)

### Comments

- `GET /api/comments` (auth, query: `commentable_type`, `commentable_id`)
- `GET /api/comments/{comment}/replies` (auth)
- `POST /api/comments` (auth, JSON: `body`, `commentable_id`, `commentable_type`, optional `parent_id`)
- `PUT /api/comments/{comment}` (auth, JSON: `body`)
- `DELETE /api/comments/{comment}` (auth)

### Notifications

- `GET /api/notifications` (auth)
- `GET /api/notifications/unread` (auth)
- `PATCH /api/notifications/{id}/read` (auth)
- `PATCH /api/notifications/read-all` (auth)

## Admin setup (for category management)

Category creation/deletion requires an admin user (`role = 'admin'`).
