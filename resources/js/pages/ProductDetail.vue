<script setup>
import { computed, onMounted, onUnmounted } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { useProductStore } from '../stores/products';
import GenerationCard from '../components/GenerationCard.vue';

const props = defineProps({ id: { type: Number, required: true } });

const store = useProductStore();
const router = useRouter();

const TYPES = [
    { value: 'product_description', label: 'Product description' },
    { value: 'ad_copy', label: 'Ad copy' },
    { value: 'title_variants', label: 'Title variants' },
    { value: 'seo_meta', label: 'SEO meta' },
];

const product = computed(() => store.byId(props.id));

onMounted(() => store.loadProduct(props.id));
onUnmounted(() => store.stopAllPolling());

async function remove() {
    await store.deleteProduct(props.id);
    router.push({ name: 'products.index' });
}
</script>

<template>
    <div v-if="product" class="space-y-8">
        <div>
            <RouterLink :to="{ name: 'products.index' }" class="text-sm text-neutral-500 dark:text-white/50 hover:text-ink dark:hover:text-white">
                ← All products
            </RouterLink>

            <div class="mt-3 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold tracking-tight">{{ product.name }}</h1>
                    <a
                        v-if="product.source_url"
                        :href="product.source_url"
                        target="_blank"
                        rel="noopener"
                        class="mt-1 inline-block text-sm text-brand hover:underline"
                    >
                        {{ product.source_url }}
                    </a>
                    <p v-if="product.notes" class="mt-2 max-w-prose text-sm text-neutral-600 dark:text-white/60">
                        {{ product.notes }}
                    </p>
                </div>

                <button
                    class="shrink-0 rounded-md border border-neutral-300 px-3 py-1.5 text-sm text-neutral-700 dark:text-white/70 hover:bg-neutral-50 dark:hover:bg-white/5"
                    @click="remove"
                >
                    Delete
                </button>
            </div>
        </div>

        <section>
            <h2 class="text-sm font-semibold uppercase tracking-tight text-neutral-500 dark:text-white/50">Generate</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                <button
                    v-for="type in TYPES"
                    :key="type.value"
                    class="rounded-md border border-neutral-300 bg-white dark:border-white/20 dark:bg-white/5 px-3 py-1.5 text-sm hover:border-brand hover:text-brand"
                    @click="store.generate(product.id, type.value)"
                >
                    {{ type.label }}
                </button>
            </div>

            <p v-if="store.error" class="mt-3 rounded-md bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300">
                {{ store.error }}
            </p>
        </section>

        <section>
            <h2 class="text-sm font-semibold uppercase tracking-tight text-neutral-500 dark:text-white/50">History</h2>

            <p v-if="!product.generations.length" class="mt-3 text-sm text-neutral-500 dark:text-white/50">
                Nothing generated yet. Pick a content type above.
            </p>

            <div v-else class="mt-3 space-y-3">
                <GenerationCard
                    v-for="generation in product.generations"
                    :key="generation.id"
                    :generation="generation"
                />
            </div>
        </section>
    </div>

    <p v-else-if="store.error" class="rounded-md bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300">
        {{ store.error }}
    </p>

    <p v-else class="text-sm text-neutral-500 dark:text-white/50">Loading…</p>
</template>
