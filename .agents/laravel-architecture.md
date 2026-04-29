# Laravel Architecture Guide

## Required Stack

- Laravel 13
- Eloquent ORM
- MySQL
- Laravel Boost MCP when available
- Pest for tests

## Architecture Direction

The application is split into three surfaces:

- Public blog routes using Blade
- Admin UI routes serving the Vue SPA shell
- Admin API routes returning JSON for the Vue SPA

Recommended route groups:

- Public: `/`, `/posts/{slug}`, `/category/{slug}`, `/tag/{slug}`, `/search`, `/sitemap.xml`, `/robots.txt`
- Admin UI: `/admin`, `/admin/dashboard`, `/admin/posts`, `/admin/categories`, `/admin/tags`, `/admin/media`, `/admin/settings`
- Admin API: `/admin/api/*`

## Backend Rules

- Use Eloquent models and relationships instead of raw SQL.
- Validate writes through Form Requests or equivalent Laravel validators.
- Keep public queries scoped to `status = published`.
- Use pagination for public and admin lists.
- Use eager loading for post category, tags, and author.
- Keep `$fillable` or `$guarded` explicit on models.
- Keep business rules in actions/services when controllers become complex.
- Use Laravel storage APIs for uploaded files.
- Use `Storage::url()` for public image URLs.

## Database Direction

The MVP schema should support:

- `users` with future-ready author fields
- `posts`
- `categories`
- `tags`
- `post_tag`
- `settings`

Future-only tables such as revisions, activity logs, editorial comments, and full media library tables should not be created unless a future milestone asks for them.

## Security Rules

- Admin routes require authentication except login.
- CSRF protection stays active for session-based admin.
- Passwords are hashed.
- Uploaded files are validated by MIME type and size.
- Rendered content must be sanitized before public display.
- Preview routes require auth or signed temporary URLs and must be noindex.

## Testing Expectations

Prioritize tests for:

- Auth protection
- Draft and archived visibility
- Post validation
- Slug uniqueness and collision
- Upload validation and WebP handling
- Sitemap and robots behavior
- Category and tag delete rules
