# Merchant Copy Studio

A small Laravel + Vue 3 app that generates storefront copy for e-commerce products:
add a product, pick a content type, and an LLM writes the description, ad copy,
title variants, or SEO meta while the UI polls for the result.

Built to be read. The interesting parts are the provider abstraction
(`app/Services/Llm`), the queued job's failure handling (`app/Jobs`), and the
tests that cover both.

## Stack

| Layer | Choice |
| --- | --- |
| Backend | Laravel 13, PHP 8.4 |
| Frontend | Vue 3 (`<script setup>`), Pinia, Vue Router, Tailwind v4 |
| Build | Vite |
| Queue | Laravel queues (database driver by default) |
| LLM | Anthropic (`claude-opus-5`) behind a driver interface |
| Tests | PHPUnit |

## Running it

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate

npm run build          # or: npm run dev
php artisan serve      # http://127.0.0.1:8000
php artisan queue:work # generation runs on the queue — without this, nothing completes
```

It runs with **no API key**. `LLM_DRIVER` defaults to `fake`, which returns
deterministic copy through the whole pipeline. To generate real copy:

```env
LLM_DRIVER=anthropic
ANTHROPIC_API_KEY=sk-ant-...
```

```bash
php artisan test
```

## How it works

```
POST /api/v1/products/{id}/generations
        │
        ├─ build the prompt, persist it on the row, return 202 immediately
        │
        └─ GenerateProductContent (queued)
                 └─ LlmClient::complete() ──> Anthropic  |  Fake
                          │
                          └─ result + token usage written back to the row

GET /api/v1/generations/{id}   ← the Pinia store polls this until is_complete
```

Generation takes seconds, which is too long to hold an HTTP request open, so the
endpoint returns `202 Accepted` with a `queued` row and the client polls. The
prompt is stored **on the row** rather than rebuilt at read time — prompt
templates change, and old output shouldn't be attributed to the current one.

### The provider abstraction

`LlmClient` is a one-method interface with two implementations:

- `AnthropicClient` — the real provider, via the official PHP SDK.
- `FakeLlmClient` — deterministic, free, offline. It is what the test suite runs
  against and what makes the app usable without a key.

The value isn't swappability for its own sake — it's that **every failure path is
testable without touching the network.** `FakeLlmClient::failWith()` lets a test
assert what happens on a rate limit versus a refusal, which is otherwise the
hardest part of LLM integration to cover.

### Failure handling

Provider calls fail in ways that ordinary HTTP calls don't, so `LlmException`
carries a `retryable` flag:

- **Retryable** (429, 5xx, timeout) — rethrown, so the queue retries with backoff.
- **Not retryable** (400, refusal) — recorded on the row and the job stops. Burning
  two more attempts on a request that will fail identically just delays the error
  the merchant is waiting on.

A safety classifier can also decline a request and still return **HTTP 200** with
an empty body and `stop_reason: "refusal"`. Reading `content[0]` blindly there
yields an empty result that looks like a success, so `AnthropicClient` checks
`stopReason` before touching content.

`failed()` closes out any row left in `processing` after the final attempt, so the
UI never polls a generation that has silently stopped moving.

## API

| Method | Path | Notes |
| --- | --- | --- |
| `GET` | `/api/v1/products` | Paginated, generations eager-loaded |
| `POST` | `/api/v1/products` | `name` required; `source_url`, `notes` optional |
| `GET` | `/api/v1/products/{id}` | |
| `DELETE` | `/api/v1/products/{id}` | Cascades to generations |
| `POST` | `/api/v1/products/{id}/generations` | `type` — returns `202` |
| `GET` | `/api/v1/generations/{id}` | Poll target; `is_complete` drives the client |

Content types: `product_description`, `ad_copy`, `title_variants`, `seo_meta`.

## Tests

```
tests/Unit/PromptBuilderTest.php              prompt assembly
tests/Feature/Api/ProductApiTest.php          CRUD + validation
tests/Feature/Api/GenerationApiTest.php       queueing, polling, bad input
tests/Feature/Jobs/GenerateProductContentTest.php  retryable vs terminal failure
```

The job tests are the ones worth reading — they cover the paths that only happen
when the provider misbehaves, which is exactly where AI-integration code tends to
be wrong in ways that still look like it works.

## Notes on the AI-assisted workflow

This was built with Claude Code. Two things that mattered more than the speed:

- **`CLAUDE.md` sets standards per repo.** Conventions, the Laravel-specific rules,
  what not to touch. The agent reads it before writing, so generated code matches
  the surrounding code instead of drifting toward a generic house style.
- **Tests are the check on generated code, not review alone.** AI-written code
  rarely fails by not running — it fails by looking right and quietly doing the
  wrong thing. The refusal path above is a good example: the happy path passes
  either way, and only a test that asserts on a `stop_reason: "refusal"` response
  catches that an empty success was being treated as real copy.
