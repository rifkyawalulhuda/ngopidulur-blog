# Ngopi Dulur Product Guide

## Source of Truth

- Primary PRD: `docs/PRD.md`
- Implementation plan: `docs/implementation-plan.md`
- Milestone tracking: `docs/milestone-status.md`

## Product Positioning

Ngopi Dulur is a personal blog CMS: lighter than WordPress, focused on writing, publishing, and maintaining a warm personal reading space.

Brand direction:

- Warm Coffee Meets Modern Tech
- Simple product, smart foundation, warm personality

## MVP Scope

Build only the personal blog CMS MVP:

- Admin authentication
- Admin dashboard
- Post CRUD with draft, published, archived
- Category CRUD
- Tag CRUD
- Featured image upload with WebP handling
- Media MVP that lists featured images tied to posts
- Public homepage, post detail, category, tag, search
- SEO basics, sitemap, robots policy
- Settings for editable blog identity

## Product Guardrails

- Interface language is Bahasa Indonesia.
- Public blog must only expose published posts.
- Draft and archived posts must not appear in public homepage, detail, category, tag, search, related posts, sitemap, or unauthenticated preview.
- Admin dashboard must keep TailAdmin Laravel as the base.
- Public blog is Blade-first.
- Admin dashboard is a full Vue SPA.
- Do not build future multi-author workflows in the MVP.
- Do not build comments, newsletter, page builder, plugin system, membership, payment, or a full media library in the MVP.

## Future-Ready Foundation

Prepare the schema and architecture for future multi-author CMS needs:

- `users.role`
- `users.bio`
- `users.avatar`
- `posts.user_id`
- Author relationships on posts

Do not activate editor/writer workflows until a later milestone explicitly asks for them.
