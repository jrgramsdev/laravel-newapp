# Deploying to Render (free)

This repo ships a `Dockerfile` and a `render.yaml` blueprint so you can host the
running Laravel app for free and share a public URL (e.g. for a recruiter to review).

## One-time deploy

1. Create a free account at **https://render.com** and connect your GitHub.
2. Click **New ▸ Blueprint**, pick the `jrgramsdev/copy-studio` repo, and choose
   the `main` branch. Render reads `render.yaml` and creates a free Docker web service.
3. Render builds the image (installs Composer + PHP deps, builds the Vite/Tailwind
   assets), then boots it via `docker/entrypoint.sh`, which runs migrations and starts
   the app on Render's `$PORT`.
4. When the build finishes, open the assigned URL: `https://laravel-newapp-XXXX.onrender.com`.

That URL is what you send to a recruiter — it serves the real app, backend and all.

### No Blueprint? Deploy manually
**New ▸ Web Service** → connect the repo → **Runtime: Docker** → **Instance type: Free**
→ Create. Then add the env vars listed below under the service's **Environment** tab.

## Environment variables

`render.yaml` sets sensible defaults. The only one worth setting by hand:

| Variable   | Value                                   | Notes                                             |
| ---------- | --------------------------------------- | ------------------------------------------------- |
| `APP_KEY`  | output of `php artisan key:generate --show` | Optional. If unset, the container generates a fresh key on each boot — fine for a demo, but sessions reset on restart. Set it once for stability. |

`APP_ENV=production`, `APP_DEBUG=false`, `DB_CONNECTION=sqlite`,
`SESSION_DRIVER=database`, and `LOG_CHANNEL=stderr` are already configured.

## Notes on the free tier

- **Cold starts:** free services sleep after ~15 min idle and take ~30–60s to wake on the
  first request. Perfectly fine for a review link.
- **Database:** the app uses SQLite on the container's ephemeral disk, so data resets on
  each deploy/restart. There's no seeded data to preserve — the landing page is stateless.
  To persist data, add a managed Postgres and set `DB_CONNECTION=pgsql` (+ the connection
  env vars Render provides).

## Run the production image locally (optional)

```bash
docker build -t copy-studio .
docker run --rm -p 8000:8000 copy-studio
# open http://127.0.0.1:8000
```
