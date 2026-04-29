# Frontend Vue TailAdmin Guide

## Required Frontend Direction

- Admin dashboard: full Vue 3 SPA
- Admin dashboard base: TailAdmin Laravel
- Public blog: Blade-first
- Public interactive widgets may use Vue only where useful
- Styling: Tailwind CSS
- Build tool: Vite 8 target

## TailAdmin Rules

- Do not replace TailAdmin Laravel with another admin template or UI kit.
- Reuse TailAdmin layout patterns, spacing, menus, dark mode, forms, tables, charts, and utility classes.
- Add Ngopi Dulur brand warmth through tokens, copy, and small visual adjustments.
- Do not leave the admin dashboard looking like an unchanged generic template at MVP completion.

## Vue SPA Rules

- Admin UI routes should serve a SPA shell.
- Data operations should go through `/admin/api/*`.
- Use Vue Router for admin client-side pages when the SPA milestone starts.
- Keep labels, empty states, validation messages, and destructive confirmations in Bahasa Indonesia.
- Use clear primary actions such as `Simpan Draft`, `Preview`, and `Terbitkan`.

## Public Blade Rules

- Public blog pages should be readable, fast, and editorial.
- Public pages must never rely on frontend-only filtering for draft or archived posts.
- Public post content should render sanitized HTML only.
- Search results are noindex by default for MVP.
- Preview pages use public layout but require auth or signed access.

## Tailwind Rules

- Follow the project Tailwind CSS version and existing TailAdmin utilities.
- Keep light and dark espresso theme tokens deliberate.
- Avoid scattering product colors as one-off classes when a reusable token is better.
- Keep responsive typography readable and avoid layout shifts in core reading pages.
