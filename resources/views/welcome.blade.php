<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <meta name="description" content="A fresh Laravel application, boosted.">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <a href="https://laravel.com/docs" class="hidden text-ink/70 transition hover:text-brand sm:inline dark:text-white/70">Docs</a>
                    <a href="https://laravel.com/docs/boost" class="hidden text-ink/70 transition hover:text-brand sm:inline dark:text-white/70">Boost</a>
                    <a href="{{ route('contact.create') }}"
                       class="rounded-full bg-brand px-4 py-2 text-white shadow-sm transition hover:bg-brand-dark">
                        Contact
                    </a>
                </nav>
            </header>

            {{-- Hero --}}
            <main class="flex flex-1 flex-col justify-center py-16">
                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-brand/30 bg-brand/10 px-4 py-1.5 text-sm font-medium text-brand-dark dark:text-brand-light">
                    <span class="size-2 rounded-full bg-brand"></span>
                    Powered by Laravel Boost
                </span>

                <h1 class="mt-6 max-w-3xl text-5xl font-bold tracking-tight sm:text-6xl">
                    Build something
                    <span class="text-brand">brilliant</span>
                    with Laravel.
                </h1>

                <p class="mt-6 max-w-2xl text-lg text-ink/70 dark:text-white/70">
                    A clean, starter-kit-free Laravel {{ Illuminate\Foundation\Application::VERSION }} application on PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }},
                    styled in white, green &amp; black and wired up with Laravel Boost for AI-assisted development.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <a href="{{ route('contact.create') }}"
                       class="rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-dark">
                        Get in touch
                    </a>
                    <a href="https://laravel.com/docs"
                       class="rounded-full border border-ink/15 px-6 py-3 font-semibold text-ink transition hover:border-brand hover:text-brand dark:border-white/20 dark:text-white dark:hover:border-brand dark:hover:text-brand-light">
                        Read the docs
                    </a>
                </div>

                {{-- Feature cards --}}
                <div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @php
                        $features = [
                            ['title' => 'No starter kit', 'body' => 'A bare Laravel install — no Breeze or Jetstream, just a clean foundation to build on.'],
                            ['title' => 'Boost included', 'body' => 'Laravel Boost is installed with AI guidelines, skills, and an MCP server for sharper code generation.'],
                            ['title' => 'White · Green · Black', 'body' => 'A crisp brand palette defined with Tailwind v4 CSS-first theme tokens.'],
                        ];
                    @endphp

                    @foreach ($features as $feature)
                        <div class="group rounded-2xl border border-ink/10 bg-white p-6 shadow-sm transition hover:border-brand hover:shadow-md dark:border-white/10 dark:bg-white/5">
                            <div class="grid size-10 place-items-center rounded-xl bg-brand/10 text-brand transition group-hover:bg-brand group-hover:text-white">
                                <span class="size-2.5 rounded-full bg-current"></span>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold">{{ $feature['title'] }}</h3>
                            <p class="mt-2 text-sm text-ink/70 dark:text-white/70">{{ $feature['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </main>

            {{-- Footer --}}
            <footer class="flex flex-col items-center justify-between gap-4 border-t border-ink/10 py-8 text-sm text-ink/60 sm:flex-row dark:border-white/10 dark:text-white/60">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Built with Laravel &amp; Boost.</p>
                <div class="flex items-center gap-2">
                    <span class="size-3 rounded-full bg-white ring-1 ring-ink/20"></span>
                    <span class="size-3 rounded-full bg-brand"></span>
                    <span class="size-3 rounded-full bg-ink ring-1 ring-ink/20 dark:bg-black dark:ring-white/20"></span>
                </div>
            </footer>
        </div>
    </body>
</html>
