# EcoLibrary API Documentation

## Base URL

`/api`

## Authentication

This API uses Laravel Sanctum Bearer tokens.

1. `POST /register`
2. `POST /login` and get `token`
3. Send header: `Authorization: Bearer {token}`

## Roles

- `reader`: can only access reader user stories (category available books, search, popular, new arrivals).
- `admin`: can do everything (reader endpoints + CRUD + statistics).

If a non-admin user calls an admin endpoint, the API returns:

```json
{
  "message": "Failed: admin access only"
}
```

with HTTP `403`.

## Public Auth Endpoints

### Register

`POST /register`

Body:

```json
{
  "name": "John Reader",
  "email": "john@example.com",
  "password": "password123"
}
```

### Login

`POST /login`

Body:

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

## Reader Endpoints (`auth:sanctum`)

### Current user

`GET /me`

### Logout

`POST /logout`

### Search books by title or category

`GET /books/search?title=clean&category=science`

`category` can be category name or category id.

### List available books in one category

`GET /categories/{id}/books`

### Popular books in one category

`GET /categories/{id}/books/popular`

### New arrivals in one category

`GET /categories/{id}/books/new`

## Admin Endpoints (`auth:sanctum` + `admin`)

### Admin read endpoints

- `GET /books`
- `GET /books/{id}`
- `GET /categories`
- `GET /categories/{id}`

### Category CRUD

- `POST /categories`
- `PUT /categories/{id}`
- `DELETE /categories/{id}`

Category body:

```json
{
  "name": "Science",
  "slug": "science",
  "description": "Science books"
}
```

### Book CRUD

- `POST /books`
- `PUT /books/{id}`
- `DELETE /books/{id}`

Book body:

```json
{
  "category_id": 1,
  "title": "Clean Architecture",
  "slug": "clean-architecture",
  "description": "Software design principles",
  "author": "Robert C. Martin",
  "total_quantity": 12,
  "available_quantity": 9,
  "is_active": true
}
```

### Collection statistics

`GET /admin/stats/collection`

Response contains:

- `collection.total_books`
- `collection.total_quantity`
- `collection.total_available`
- `collection.total_degraded`
- `collection.active_books`
- `collection.inactive_books`
- `top_viewed_books` (top consulted books)

### Degraded books statistics

`GET /admin/stats/degraded-books`

Response contains each book with computed `degraded_quantity` and a global `total_degraded_quantity`.

## Seeded test accounts

After `php artisan migrate:fresh --seed`:

- Admin: `admin@example.com` / `password`
- Reader: `test@example.com` / `password`
