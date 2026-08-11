<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Contact · {{ config('app.name', 'Laravel') }}</title>
        <meta name="description" content="Get in touch.">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-ink antialiased dark:bg-ink dark:text-white">
        <div class="mx-auto flex min-h-screen max-w-2xl flex-col px-6">
            {{-- Navbar --}}
            <header class="flex items-center justify-between gap-4 py-6">
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-semibold">
                    <span class="grid size-8 place-items-center rounded-lg bg-brand text-white">L</span>
                    <span>{{ config('app.name', 'Laravel') }}</span>
                </a>
                <a href="{{ url('/') }}" class="text-sm font-medium text-ink/70 transition hover:text-brand dark:text-white/70">
                    &larr; Back home
                </a>
            </header>

            <main class="flex flex-1 flex-col justify-center py-12">
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                    Get in <span class="text-brand">touch</span>
                </h1>
                <p class="mt-4 text-lg text-ink/70 dark:text-white/70">
                    Have a question or want to work together? Send a message below.
                </p>

                {{-- Success flash --}}
                @if (session('status'))
                    <div role="alert"
                         class="mt-8 flex items-center gap-3 rounded-xl border border-brand/30 bg-brand/10 px-4 py-3 text-sm font-medium text-brand-dark dark:text-brand-light">
                        <span class="grid size-5 shrink-0 place-items-center rounded-full bg-brand text-white">&checkmark;</span>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="mt-8 flex flex-col gap-6" novalidate>
                    @csrf

                    {{-- Name --}}
                    <div class="flex flex-col gap-2">
                        <label for="name" class="text-sm font-semibold">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               autocomplete="name"
                               class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-ink outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30 dark:border-white/15 dark:bg-white/5 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="flex flex-col gap-2">
                        <label for="email" class="text-sm font-semibold">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               autocomplete="email"
                               class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-ink outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30 dark:border-white/15 dark:bg-white/5 dark:text-white @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Message --}}
                    <div class="flex flex-col gap-2">
                        <label for="message" class="text-sm font-semibold">Message</label>
                        <textarea name="message" id="message" rows="5" required
                                  class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-ink outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30 dark:border-white/15 dark:bg-white/5 dark:text-white @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-fit rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-dark">
                        Send message
                    </button>
                </form>
            </main>

            <footer class="border-t border-ink/10 py-8 text-sm text-ink/60 dark:border-white/10 dark:text-white/60">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Built with Laravel &amp; Boost.</p>
            </footer>
        </div>
    </body>
</html>
