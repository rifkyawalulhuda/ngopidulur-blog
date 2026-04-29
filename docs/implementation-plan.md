# Implementation Plan - Ngopi Dulur

Last updated: 2026-04-29

## Source of Truth

- `docs/PRD.md`
- `/.agents/ngopi-dulur-product.md`
- `/.agents/laravel-architecture.md`
- `/.agents/frontend-vue-tailadmin.md`
- `/.agents/rtk-command-policy.md`
- `docs/milestone-status.md`

## Product Principle

Simple product, smart foundation, warm personality.

Brand direction: Warm Coffee Meets Modern Tech.

## Target Stack From PRD

- Laravel 13
- Eloquent ORM
- MySQL
- Vue 3
- Vite 8
- Tailwind CSS
- TailAdmin Laravel for admin dashboard
- Public blog with Blade
- Admin dashboard as full Vue SPA

## Current Repo Audit

Milestone 00 audit found the repository is currently a TailAdmin Laravel starter, not yet the Ngopi Dulur CMS implementation.

- Laravel runtime: `Laravel Framework 12.26.4`
- `composer.json`: `laravel/framework:^12.0`, `laravel/boost:^1.1`, Pest 4, Laravel Pint, Sail, Pail
- PHP runtime from `php artisan about`: `8.4.20`
- Database driver from `php artisan about`: SQLite in current environment
- `package.json`: Vite `^7.0.4`, Tailwind CSS `^4.1.12`, `@tailwindcss/vite`, Alpine, ApexCharts, FullCalendar, TailAdmin support libraries
- Vue 3 dependency: not present in `package.json`
- Vite 8 dependency: not present yet
- TailAdmin status: TailAdmin Laravel Blade template is present, including layout, sidebar, dashboard, forms, tables, UI components, charts, auth sample pages, and `tailadmin-laravel.png`
- Admin Vue SPA status: not present yet; current JS uses Alpine and component-specific scripts
- Public Blade blog status: not present yet; current `/` route renders TailAdmin ecommerce dashboard sample
- Routes status: sample TailAdmin routes only; no `/admin`, `/admin/api`, post, category, tag, search, sitemap, or robots routes yet
- Models status: only default `User` model exists
- Migrations status: default users, cache, and jobs migrations only
- Git status: unavailable because this working directory does not contain a `.git` directory
- Laravel Boost package status: `laravel/boost v1.1.5` is installed and `boost.json` has `"mcp": true`, but Laravel Boost MCP tools are not available in this Codex session and `php artisan boost:*` commands are not registered
- Context7 usage: used during Milestone 00 to verify Laravel Boost install/MCP role

## Architecture Plan

### Public Blog

- Use Blade pages for homepage, post detail, category, tag, search, sitemap, robots, and error pages.
- All public post queries must filter `status = published`.
- Public content must render sanitized `rendered_content`.
- Draft and archived content must return 404 on public detail routes.

### Admin Dashboard

- Preserve TailAdmin Laravel as the visual base.
- Convert admin surface into a Vue 3 SPA in the foundation milestone.
- Keep admin UI routes as SPA entry points and data operations in `/admin/api/*`.
- Keep all admin-facing labels and validation messages in Bahasa Indonesia.

### Backend

- Use Eloquent ORM.
- Use Form Requests or equivalent Laravel validators for writes.
- Use actions/services when domain rules become more than simple CRUD.
- Prepare multi-author-ready schema without activating future editorial workflows.

## Milestone Plan

| Milestone | Scope |
|---|---|
| 00 Bootstrap & Audit | Repo audit, agent guidelines, implementation plan, milestone status |
| 01 Foundation | Align stack to PRD, setup Laravel 13 target, Vue 3 SPA foundation, Vite 8 target, MySQL config, TailAdmin admin shell, public Blade shell |
| 02 Database & Models | Users future fields, posts, categories, tags, settings, relationships, factories, seeders |
| 03 Dashboard, Category, Tag | Admin dashboard data, category CRUD, tag CRUD |
| 04 Post CRUD, Editor, Upload, Preview | Post CRUD, rich text and markdown storage, validation, featured image, secure preview |
| 05 Public Blog | Homepage, post detail, category, tag, search, pagination, public theme |
| 06 Settings, Media, SEO, Sitemap, Robots | Settings, Media MVP, SEO metadata, sitemap, robots/noindex policy |
| 07 Tests, Hardening, Polish | Required MVP tests, security/performance hardening, responsive polish |
| 08 Final Verification | Full acceptance pass and launch readiness |

## Non-Negotiable Constraints

- Do not replace TailAdmin Laravel.
- Do not build product auth before the auth milestone.
- Do not build product migrations before the database milestone.
- Do not build UI product screens before their milestone.
- Do not create future multi-author workflow UI in the MVP.
- Public blog remains Blade-first.
- Admin dashboard becomes full Vue SPA.
- Draft and archived posts must never leak publicly.
- Use RTK for verbose terminal output.
- Use Laravel Boost MCP when available.
- Use Context7 only when current documentation is needed for a package/framework decision.

## Known Risks

- Current repo stack does not yet match PRD target: Laravel 12 vs Laravel 13, Vite 7 vs Vite 8, Vue missing, SQLite environment vs MySQL target.
- Laravel Boost package exists but MCP tooling is not callable from this session.
- Git metadata is unavailable in this folder, so normal `git status` and `git diff` verification cannot run until the repo is under Git or Codex is pointed at the actual checkout root.
- Current TailAdmin routes are public sample routes, not protected admin routes.
- Current login/signup pages are template pages, not product auth.

## Decisions

- Milestone 00 records gaps only; it does not upgrade Laravel, Vite, or add Vue because that belongs to Milestone 01.
- TailAdmin Laravel is treated as a preserved asset and future admin base, not as product UI completion.
- `docs/PRD.md` is the PRD path in this repo even though some prompts refer to `PRD.md` at root.

## Deferred Features

- Multi-author workflow
- Editor/writer roles in UI
- Activity log
- Revision history
- Full media library
- Comments
- Newsletter
- Plugin system
- Page builder
