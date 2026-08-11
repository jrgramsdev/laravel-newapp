<script setup>
import { computed } from 'vue';

const props = defineProps({
    generation: { type: Object, required: true },
});

const STATUS_STYLES = {
    queued: 'bg-neutral-100 text-neutral-600',
    processing: 'bg-amber-100 text-amber-700',
    completed: 'bg-brand/10 text-brand-dark',
    failed: 'bg-red-100 text-red-700',
};

const badgeClass = computed(() => STATUS_STYLES[props.generation.status] ?? STATUS_STYLES.queued);
const inFlight = computed(() => !props.generation.is_complete);

const usage = computed(() => {
    const g = props.generation;
    if (g.status !== 'completed') return null;

    const parts = [];
    if (g.model) parts.push(g.model);
    if (g.input_tokens != null) parts.push(`${g.input_tokens} in / ${g.output_tokens} out`);
    if (g.duration_ms != null) parts.push(`${(g.duration_ms / 1000).toFixed(1)}s`);
    return parts.join(' · ');
});

async function copy() {
    await navigator.clipboard.writeText(props.generation.result);
}
</script>

<template>
    <article class="rounded-lg border border-neutral-200 bg-white p-4">
        <header class="flex items-center justify-between gap-3">
            <h3 class="text-sm font-medium">{{ generation.type_label }}</h3>
            <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="badgeClass">
                {{ generation.status }}
            </span>
        </header>

        <p v-if="inFlight" class="mt-3 text-sm text-neutral-500">
            Working… this usually takes a few seconds.
        </p>

        <p v-else-if="generation.status === 'failed'" class="mt-3 text-sm text-red-700">
            {{ generation.error }}
        </p>

        <template v-else>
            <pre class="mt-3 whitespace-pre-wrap font-sans text-sm text-neutral-800">{{ generation.result }}</pre>

            <footer class="mt-3 flex items-center justify-between gap-3 border-t border-neutral-100 pt-3">
                <span class="text-xs text-neutral-500">{{ usage }}</span>
                <button class="text-xs font-medium text-brand hover:underline" @click="copy">Copy</button>
            </footer>
        </template>
    </article>
</template>
