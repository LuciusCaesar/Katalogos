<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-950">
        {{-- Header --}}
        <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-5">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="flex size-8 items-center justify-center rounded-md bg-neutral-900 text-white dark:bg-white dark:text-neutral-900">
                    <x-app-logo-icon class="size-5 fill-current" />
                </span>
                <span class="text-lg font-semibold text-neutral-900 dark:text-white">Katalogos</span>
            </a>
            <nav class="flex items-center gap-2">
                @auth
                    <flux:button :href="route('dashboard')" variant="primary" icon="arrow-right">
                        {{ __('Go to dashboard') }}
                    </flux:button>
                @else
                    <flux:button :href="route('login')" variant="ghost">
                        {{ __('Log in') }}
                    </flux:button>
                    @if (Route::has('register'))
                        <flux:button :href="route('register')" variant="primary">
                            {{ __('Register') }}
                        </flux:button>
                    @endif
                @endauth
            </nav>
        </header>

        <main class="mx-auto w-full max-w-6xl px-6 pb-16">
            {{-- Hero --}}
            <section class="py-16 text-center lg:py-24">
                <flux:badge color="blue" icon="circle-stack" class="mx-auto">
                    {{ __('Data governance catalog') }}
                </flux:badge>
                <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-bold tracking-tight text-neutral-900 sm:text-5xl dark:text-white">
                    {{ __('Every piece of our data, documented and owned.') }}
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-neutral-600 dark:text-neutral-400">
                    {{ __('Katalogos is the company’s data catalog: what our data means, who is responsible for it, and how healthy it is.') }}
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    @auth
                        <flux:button :href="route('dashboard')" variant="primary" icon="arrow-right">
                            {{ __('Open the catalog') }}
                        </flux:button>
                    @else
                        <flux:button :href="route('login')" variant="primary" icon="arrow-right">
                            {{ __('Browse the catalog') }}
                        </flux:button>
                        @if (Route::has('register'))
                            <flux:button :href="route('register')" variant="outline">
                                {{ __('Create an account') }}
                            </flux:button>
                        @endif
                    @endauth
                </div>
            </section>

            {{-- Three pillars --}}
            <section class="grid gap-6 md:grid-cols-3">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <flux:icon icon="book-open" variant="outline" class="size-6" />
                    </div>
                    <h2 class="mt-4 text-lg font-semibold text-neutral-900 dark:text-white">
                        {{ __('Business glossary') }}
                    </h2>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                        {{ __('Domains, business assets, KPIs and regulations — with a plain-language definition for each.') }}
                    </p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <flux:badge color="zinc" size="sm">{{ __('Domains') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('Business Assets') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('KPIs') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('Regulations') }}</flux:badge>
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-green-500/10 text-green-600 dark:text-green-400">
                        <flux:icon icon="shield-check" variant="outline" class="size-6" />
                    </div>
                    <h2 class="mt-4 text-lg font-semibold text-neutral-900 dark:text-white">
                        {{ __('Quality & governance') }}
                    </h2>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                        {{ __('Business rules, data quality checks, issues, root causes and their solutions.') }}
                    </p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <flux:badge color="zinc" size="sm">{{ __('Business Rules') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('Data Quality Checks') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('Data Issues') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('Root Causes') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('Solutions') }}</flux:badge>
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400">
                        <flux:icon icon="share" variant="outline" class="size-6" />
                    </div>
                    <h2 class="mt-4 text-lg font-semibold text-neutral-900 dark:text-white">
                        {{ __('Lineage & impact') }}
                    </h2>
                    <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                        {{ __('Data sources, processes and events — and the impact diagram around any asset.') }}
                    </p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <flux:badge color="zinc" size="sm">{{ __('Data Sources') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('Business Processes') }}</flux:badge>
                        <flux:badge color="zinc" size="sm">{{ __('Business Events') }}</flux:badge>
                    </div>
                </div>
            </section>

            {{-- How it works --}}
            <section class="mt-20">
                <h2 class="text-center text-2xl font-semibold text-neutral-900 dark:text-white">
                    {{ __('How it works') }}
                </h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <div class="text-center">
                        <div class="mx-auto flex size-10 items-center justify-center rounded-full bg-neutral-100 text-lg font-bold text-neutral-600 dark:bg-zinc-800 dark:text-neutral-300">
                            1
                        </div>
                        <h3 class="mt-4 font-semibold text-neutral-900 dark:text-white">{{ __('Find') }}</h3>
                        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ __('Search every entity from the header, or browse by domain, initiative or quality theme.') }}
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex size-10 items-center justify-center rounded-full bg-neutral-100 text-lg font-bold text-neutral-600 dark:bg-zinc-800 dark:text-neutral-300">
                            2
                        </div>
                        <h3 class="mt-4 font-semibold text-neutral-900 dark:text-white">{{ __('Understand') }}</h3>
                        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ __('Every entry shows its name, its definition and the people responsible for it.') }}
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex size-10 items-center justify-center rounded-full bg-neutral-100 text-lg font-bold text-neutral-600 dark:bg-zinc-800 dark:text-neutral-300">
                            3
                        </div>
                        <h3 class="mt-4 font-semibold text-neutral-900 dark:text-white">{{ __('Act') }}</h3>
                        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                            {{ __('Stewards and owners manage definitions, teams and quality checks; everyone can see impact.') }}
                        </p>
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-neutral-200 dark:border-neutral-800">
            <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-4 px-6 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                <span>Katalogos</span>
                <div class="flex items-center gap-4">
                    <a href="https://github.com/LuciusCaesar/Katalogos" target="_blank" class="hover:text-neutral-900 dark:hover:text-white">
                        {{ __('Documentation') }}
                    </a>
                    <a href="https://github.com/LuciusCaesar/Katalogos" target="_blank" class="hover:text-neutral-900 dark:hover:text-white">
                        {{ __('Repository') }}
                    </a>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
