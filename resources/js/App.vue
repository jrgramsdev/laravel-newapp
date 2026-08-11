<script setup>
import { RouterLink, RouterView } from 'vue-router';
</script>

<template>
    <div class="min-h-screen">
        <header class="border-b border-neutral-200 bg-white">
            <div class="mx-auto flex max-w-4xl items-center justify-between px-6 py-4">
                <RouterLink :to="{ name: 'products.index' }" class="flex items-center gap-2">
                    <span class="inline-block size-2.5 rounded-full bg-brand"></span>
                    <span class="text-sm font-semibold tracking-tight">Merchant Copy Studio</span>
                </RouterLink>
                <span class="text-xs text-neutral-500">AI product content for storefronts</span>
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-6 py-10">
            <RouterView />
        </main>
    </div>
</template>
