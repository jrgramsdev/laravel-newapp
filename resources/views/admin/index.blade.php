<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Admin · {{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-ink antialiased dark:bg-ink dark:text-white">
        <div class="mx-auto flex min-h-screen max-w-5xl flex-col px-6">
            {{-- Navbar --}}
            <header class="flex items-center justify-between gap-4 py-6">
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-semibold">
                    <span class="grid size-8 place-items-center rounded-lg bg-brand text-white">L</span>
                    <span>{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex items-center gap-4 text-sm font-medium">
                    <span class="hidden text-ink/60 sm:inline dark:text-white/60">{{ auth()->user()->email }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="rounded-full border border-ink/15 px-4 py-2 transition hover:border-brand hover:text-brand dark:border-white/20 dark:hover:border-brand dark:hover:text-brand-light">
                            Sign out
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 py-8">
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold tracking-tight">Contact submissions</h1>
                    <span class="rounded-full bg-brand/10 px-3 py-1 text-sm font-semibold text-brand-dark dark:text-brand-light">
                        {{ $messages->total() }}
                    </span>
                </div>

                @if ($messages->isEmpty())
                    <div class="mt-8 rounded-2xl border border-dashed border-ink/15 p-10 text-center text-ink/60 dark:border-white/15 dark:text-white/60">
                        No messages yet. Submissions from the
                        <a href="{{ route('contact.create') }}" class="text-brand hover:underline">contact form</a>
                        will appear here.
                    </div>
                @else
                    <div class="mt-8 overflow-x-auto rounded-2xl border border-ink/10 dark:border-white/10">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-ink/10 bg-ink/[0.02] text-xs uppercase tracking-wide text-ink/60 dark:border-white/10 dark:bg-white/5 dark:text-white/60">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Name</th>
                                    <th class="px-4 py-3 font-semibold">Email</th>
                                    <th class="px-4 py-3 font-semibold">Message</th>
                                    <th class="px-4 py-3 font-semibold whitespace-nowrap">Received</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink/5 dark:divide-white/5">
                                @foreach ($messages as $message)
                                    <tr class="align-top transition hover:bg-brand/[0.04]">
                                        <td class="px-4 py-3 font-medium">{{ $message->name }}</td>
                                        <td class="px-4 py-3">
                                            <a href="mailto:{{ $message->email }}" class="text-brand hover:underline">{{ $message->email }}</a>
                                        </td>
                                        <td class="px-4 py-3 text-ink/80 dark:text-white/80">{{ $message->message }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-ink/60 dark:text-white/60">
                                            {{ $message->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $messages->links() }}
                    </div>
                @endif
            </main>
        </div>
    </body>
</html>
