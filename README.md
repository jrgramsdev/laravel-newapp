# laravel-newapp

A clean **Laravel 13** application — no starter kit — styled in a **white / green / black**
theme and wired up with [Laravel Boost](https://laravel.com/docs/boost) for AI-assisted
development. Built on **PHP 8.4**, **Tailwind CSS v4**, and **SQLite**, and packaged to deploy
for free.

**▶ Live demo: [laravel-newapp.onrender.com](https://laravel-newapp.onrender.com/)**
&nbsp;·&nbsp; **Deploy your own:** [DEPLOYMENT.md](DEPLOYMENT.md)

![Landing page — white/green/black theme](docs/screenshot.png)

---

## What this demonstrates

- Standing up a **Laravel 13** app from scratch (no Breeze/Jetstream scaffolding).
- **Tailwind CSS v4** with a CSS-first `@theme` design-token workflow and dark-mode support.
- A production-minded **Docker** build and a one-click **Render** deployment blueprint.
- A tidy developer experience: single-command dev server, a `push.sh` helper, and
  **Laravel Boost** guidelines/skills committed for consistent AI-assisted iteration.

## Tech stack

| Area       | Choice                                                              |
| ---------- | ------------------------------------------------------------------ |
| Framework  | Laravel 13 (PHP 8.4)                                                |
| Front-end  | Blade + Tailwind CSS v4 via Vite                                    |
| Database   | SQLite (zero-config; swappable for Postgres/MySQL)                  |
| Tooling    | Laravel Boost (AI guidelines, skills, MCP server)                  |
| Deploy     | Docker + Render blueprint (`render.yaml`)                           |

## Quick start (local)

**Prerequisites:** PHP 8.2+, Composer, and Node 18+.
On macOS, [Laravel Herd](https://herd.laravel.com) bundles PHP + Composer in one install.

```bash
git clone https://github.com/jrgramsdev/laravel-newapp.git
cd laravel-newapp

composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate

composer run dev        # starts the server + Vite hot-reload in one command
```

Open **http://127.0.0.1:8000**. Edit `resources/views/welcome.blade.php` or
`resources/css/app.css` and the page reloads automatically.

### Save & ship your changes

```bash
./push.sh "describe your change"   # stage → commit → rebase → push
```

Pushing to `main` auto-deploys the live site on Render.

## Brand palette

Defined as Tailwind v4 theme tokens in [`resources/css/app.css`](resources/css/app.css):

| Token                 | Value     | Role              |
| --------------------- | --------- | ----------------- |
| `--color-brand`       | `#16a34a` | Primary green     |
| `--color-brand-light` | `#22c55e` | Lighter green     |
| `--color-brand-dark`  | `#15803d` | Darker green      |
| `--color-ink`         | `#0a0a0a` | Near-black text   |

Use them as utilities anywhere: `bg-brand`, `text-brand`, `text-ink`, `border-brand`, …

## Deployment

Free hosting is preconfigured for [Render](https://render.com) via `Dockerfile` +
`render.yaml`. Full step-by-step instructions — including environment variables and
free-tier notes — are in **[DEPLOYMENT.md](DEPLOYMENT.md)**.

## Laravel Boost

Boost is installed as a dev dependency. Its MCP server is registered in `.mcp.json`, so
Claude Code (and other configured agents) can use Boost tools such as `search-docs`,
`tinker`, and `database-query`. Curated Laravel guidelines live in `CLAUDE.md` and skills
under `.claude/skills/`. Re-run setup any time with `php artisan boost:install`.

## Tests

```bash
php artisan test
```
