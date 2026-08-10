<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sign in · {{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-ink antialiased dark:bg-ink dark:text-white">
        <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6">
            <a href="{{ url('/') }}" class="mb-8 flex items-center gap-2 text-lg font-semibold">
                <span class="grid size-8 place-items-center rounded-lg bg-brand text-white">L</span>
                <span>{{ config('app.name', 'Laravel') }}</span>
            </a>

            <h1 class="text-3xl font-bold tracking-tight">Sign in</h1>
            <p class="mt-2 text-sm text-ink/70 dark:text-white/70">Access the admin dashboard.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-8 flex flex-col gap-6" novalidate>
                @csrf

                <div class="flex flex-col gap-2">
                    <label for="email" class="text-sm font-semibold">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           autocomplete="username"
                           class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-ink outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30 dark:border-white/15 dark:bg-white/5 dark:text-white @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="password" class="text-sm font-semibold">Password</label>
                    <input type="password" name="password" id="password" required
                           autocomplete="current-password"
                           class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-ink outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30 dark:border-white/15 dark:bg-white/5 dark:text-white">
                </div>

                <label class="flex items-center gap-2 text-sm text-ink/70 dark:text-white/70">
                    <input type="checkbox" name="remember" class="rounded border-ink/30 text-brand focus:ring-brand/30">
                    Remember me
                </label>

                <button type="submit"
                        class="rounded-full bg-brand px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-brand-dark">
                    Sign in
                </button>
            </form>
        </div>
    </body>
</html>
