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

Milestone 00 audited the starter state. Milestone 01 completed the foundation layer needed to begin product work. Milestone 02 completed the MVP database and model layer. Milestone 03 completed the admin dashboard stats, category CRUD, tag CRUD, and TailAdmin-aware Vue admin pages. Milestone 04 completed the post management layer with editor, upload, preview, and public visibility guard. Milestone 05 completed the public Blade blog with featured fallback, taxonomy pages, search, public theme, robots, and branded 404.

- Laravel runtime: `Laravel Framework 13.7.0`
- `composer.json`: `laravel/framework:^13.5`, `laravel/tinker:^3.0`, `laravel/boost:^2.4`, Laravel Pint, Sail, Pail, Pest 5 dev branch
- PHP runtime from `php artisan about`: `8.4.20`
- Database driver from `php artisan about`: SQLite in the current checkout environment
- `package.json`: Vite `^8.0.10`, `laravel-vite-plugin:^3.0.1`, Tailwind CSS `^4.2.4`, `@tailwindcss/vite:^4.2.4`, Vue `^3.5.33`, Alpine, ApexCharts, FullCalendar, TailAdmin support libraries
- Vite 8 dependency: present and build verified
- TailAdmin status: TailAdmin Laravel Blade template is still the base admin layout system, with warm Ngopi Dulur branding layered on top
- Admin Vue SPA status: foundation shell present at `/admin/{any?}` with Vue mount in `resources/js/admin.js`
- Public Blade blog status: Blade public homepage, post detail, category, tag, search, theme toggle, robots, and branded 404 are present
- Routes status: admin auth/session routes, protected dashboard route, public blog routes, robots.txt, and SPA catchall routes are present
- Models status: `User`, `Post`, `Category`, `Tag`, and `SiteSetting` models exist with core relationships and scopes
- Migrations status: user blog fields plus `posts`, `categories`, `tags`, `post_tag`, and `site_settings` tables are present
- Git status: available in this checkout; current worktree contains modified and untracked files for this milestone
- Laravel Boost package status: `laravel/boost v2.4.6` is installed and `boost.json` has `"mcp": true`, but Laravel Boost MCP tools are not available in this Codex session and `php artisan boost:*` commands are not registered
- Context7 usage: used during Milestone 00 to verify Laravel Boost install/MCP role

## Architecture Plan

### Public Blog

- Use Blade pages for homepage, post detail, category, tag, search, sitemap, robots, and error pages.
- All public post queries must filter `status = published`.
- Public content must render sanitized `rendered_content`.
- Draft and archived content must return 404 on public detail routes.

### Admin Dashboard

- Preserve TailAdmin Laravel as the visual base.
- Convert admin surface into a Vue 3 SPA in later product milestones.
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
| 01 Foundation | Align stack to PRD, setup Laravel 13 target, Vue 3 SPA foundation, Vite 8 target, TailAdmin admin shell, public Blade shell |
| 02 Database & Models | Users future fields, posts, categories, tags, settings, relationships, factories, seeders |
| 03 Dashboard, Category, Tag | Admin dashboard stats, category CRUD, tag CRUD, admin Vue pages, and delete-rule handling |
| 04 Post CRUD, Editor, Upload, Preview | Post CRUD, rich text and markdown storage, validation, featured image, WebP upload, secure preview, and public visibility guard |
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

## Milestone 05 Notes

- Public blog pages are now implemented with Blade and use published-only queries.
- Theme preference supports light and dark espresso through a local browser preference with a default pulled from site settings.
- `robots.txt` is in place, but `sitemap.xml` and fuller SEO treatment are still left for the SEO milestone.

## Known Risks

- Current repo stack still does not fully match the eventual PRD target because the current environment still uses SQLite instead of MySQL, so the full-text index is defined conditionally for MySQL/MariaDB targets.
- Pest/Laravel 13 compatibility currently uses dev branch constraints for Pest 5 and Collision until stable package releases are available.
- Laravel Boost package exists but MCP tooling is not callable from this session.
- Git metadata is now present, but future work should still keep diffs tight because TailAdmin remains large.
- Current TailAdmin routes and views were preserved as the base and are now being progressively adapted to Ngopi Dulur branding.

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
