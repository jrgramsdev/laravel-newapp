<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} — Laravel + Vue 3 demo</title>
        <meta name="description" content="A Laravel 13 and Vue 3 application: a queued AI copy generator, hand-rolled session auth, and a validated contact form.">

        {{-- CSS only. resources/js/app.js is the Vue SPA entry and mounts on #app,
             which only exists in the studio shell (resources/views/app.blade.php). --}}
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-white text-ink antialiased dark:bg-ink dark:text-white">
        <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-6">
            {{-- Navbar --}}
            <header class="flex items-center justify-between gap-4 py-6">
                <a href="/" class="flex items-center gap-2 text-lg font-semibold">
                    <span class="grid size-8 place-items-center rounded-lg bg-brand text-white">L</span>
                    <span>{{ config('app.name', 'Laravel') }}</span>
                </a>
                <nav class="flex items-center gap-6 text-sm font-medium">
                    <a href="https://github.com/jrgramsdev/laravel-newapp" class="hidden text-ink/70 transition hover:text-brand sm:inline dark:text-white/70">Source</a>
                    <a href="{{ route('contact.create') }}" class="hidden text-ink/70 transition hover:text-brand sm:inline dark:text-white/70">Contact</a>
                    <a href="{{ url('/studio') }}"
                       class="rounded-full bg-brand px-4 py-2 text-white shadow-sm transition hover:bg-brand-dark">
                        Open the Studio
                    </a>
                </nav>
            </header>

            {{-- Hero --}}
            <main class="flex flex-1 flex-col justify-center py-16">
                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-brand/30 bg-brand/10 px-4 py-1.5 text-sm font-medium text-brand-dark dark:text-brand-light">
                    <span class="size-2 rounded-full bg-brand"></span>
                    Laravel {{ Illuminate\Foundation\Application::VERSION }} · Vue 3 · PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}
                </span>

                <h1 class="mt-6 max-w-3xl text-5xl font-bold tracking-tight sm:text-6xl">
                    AI product copy,
                    <span class="text-brand">generated on a queue.</span>
                </h1>

                <p class="mt-6 max-w-2xl text-lg text-ink/70 dark:text-white/70">
                    A full-stack demo built without a starter kit: a Vue 3 single-page studio that
                    generates storefront copy through a queued job and a pluggable LLM provider,
                    alongside hand-rolled session auth and a validated contact form.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <a href="{{ url('/studio') }}"
                       class="rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-dark">
                        Open the Studio
                    </a>
                    <a href="https://github.com/jrgramsdev/laravel-newapp"
                       class="rounded-full border border-ink/15 px-6 py-3 font-semibold text-ink transition hover:border-brand hover:text-brand dark:border-white/20 dark:text-white dark:hover:border-brand dark:hover:text-brand-light">
                        Read the source
                    </a>
                </div>

                {{-- Feature cards --}}
                <div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @php
                        $features = [
                            [
                                'title' => 'Queued AI generation',
                                'body' => 'A Vue 3 + Pinia studio posts a job, gets a 202 back, and polls until the copy lands — so a slow provider never blocks a request.',
                                'href' => url('/studio'),
                                'cta' => 'Try it',
                            ],
                            [
                                'title' => 'A swappable provider',
                                'body' => 'The LLM sits behind a one-method interface with a deterministic fake, so every failure path is testable offline and the app runs with no API key.',
                                'href' => 'https://github.com/jrgramsdev/laravel-newapp#the-provider-abstraction',
                                'cta' => 'How it works',
                            ],
                            [
                                'title' => 'Auth without a starter kit',
                                'body' => 'Session login with throttled attempts, a guarded admin dashboard, and Form Request validation — written by hand, covered by tests.',
                                'href' => route('contact.create'),
                                'cta' => 'See the form',
                            ],
                        ];
                    @endphp

                    @foreach ($features as $feature)
                        <a href="{{ $feature['href'] }}"
                           class="group flex flex-col rounded-2xl border border-ink/10 bg-white p-6 shadow-sm transition hover:border-brand hover:shadow-md dark:border-white/10 dark:bg-white/5">
                            <div class="grid size-10 place-items-center rounded-xl bg-brand/10 text-brand transition group-hover:bg-brand group-hover:text-white">
                                <span class="size-2.5 rounded-full bg-current"></span>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold">{{ $feature['title'] }}</h3>
                            <p class="mt-2 flex-1 text-sm text-ink/70 dark:text-white/70">{{ $feature['body'] }}</p>
                            <span class="mt-4 text-sm font-medium text-brand">{{ $feature['cta'] }} &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </main>

            {{-- Footer --}}
            <footer class="flex flex-col items-center justify-between gap-4 border-t border-ink/10 py-8 text-sm text-ink/60 sm:flex-row dark:border-white/10 dark:text-white/60">
                <p>
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Built with Laravel, Vue &amp; Claude Code.
                    <a href="{{ route('admin.index') }}" class="ml-2 transition hover:text-brand">Admin</a>
                </p>
                <div class="flex items-center gap-2">
                    <span class="size-3 rounded-full bg-white ring-1 ring-ink/20"></span>
                    <span class="size-3 rounded-full bg-brand"></span>
                    <span class="size-3 rounded-full bg-ink ring-1 ring-ink/20 dark:bg-black dark:ring-white/20"></span>
                </div>
            </footer>
        </div>
    </body>
</html>
