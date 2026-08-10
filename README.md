# laravel-newapp

A fresh **Laravel 13** application built **without a starter kit**, wired up with
[Laravel Boost](https://laravel.com/docs/boost) for AI-assisted development and styled
in a **white / green / black** color scheme.

## Stack

- **Laravel** 13.x on **PHP** 8.4
- **SQLite** database (zero-config; `database/database.sqlite`)
- **Tailwind CSS v4** via Vite (CSS-first `@theme` configuration)
- **Laravel Boost** — AI guidelines (`CLAUDE.md`), agent skills (`.claude/skills/`),
  and an MCP server (`.mcp.json` → `php artisan boost:mcp`)

## Brand palette

Defined as Tailwind v4 theme tokens in `resources/css/app.css`:

| Token                  | Value     |
| ---------------------- | --------- |
| `--color-brand`        | `#16a34a` |
| `--color-brand-light`  | `#22c55e` |
| `--color-brand-dark`   | `#15803d` |
| `--color-ink`          | `#0a0a0a` |

Use them as utilities: `bg-brand`, `text-brand`, `text-ink`, etc.

## Getting started

```bash
composer install
npm install
cp .env.example .env        # then set APP_KEY
php artisan key:generate
touch database/database.sqlite
php artisan migrate

# in two terminals (or use the dev script)
npm run dev
php artisan serve
```

Then open http://127.0.0.1:8000.

## Laravel Boost

Boost is installed as a dev dependency. Its MCP server is registered in `.mcp.json`, so
Claude Code (and other configured agents) can use Boost tools such as `search-docs`,
`tinker`, and `database-query`. The curated Laravel guidelines live in `CLAUDE.md` and the
skills under `.claude/skills/`. Re-run setup any time with:

```bash
php artisan boost:install
```
