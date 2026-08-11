<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { useProductStore } from '../stores/products';

const store = useProductStore();
const router = useRouter();

const form = ref({ name: '', source_url: '', notes: '' });
const fieldErrors = ref({});
const saving = ref(false);

onMounted(() => store.loadProducts());

async function submit() {
    saving.value = true;
    fieldErrors.value = {};

    try {
        const product = await store.createProduct({
            name: form.value.name,
            source_url: form.value.source_url || null,
            notes: form.value.notes || null,
        });
        form.value = { name: '', source_url: '', notes: '' };
        router.push({ name: 'products.show', params: { id: product.id } });
    } catch (e) {
        fieldErrors.value = e.errors ?? {};
        if (!Object.keys(fieldErrors.value).length) {
            store.error = e.message;
        }
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div class="space-y-10">
        <section>
            <h1 class="text-xl font-semibold tracking-tight">Add a product</h1>
            <p class="mt-1 text-sm text-neutral-600">
                Give it a name — a source URL and notes make the generated copy more specific.
            </p>

            <form class="mt-5 space-y-4" @submit.prevent="submit">
                <div>
                    <label for="name" class="block text-sm font-medium">Product name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="Ceramic pour-over dripper"
                        class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/20"
                    />
                    <p v-if="fieldErrors.name" class="mt-1 text-sm text-red-600">{{ fieldErrors.name[0] }}</p>
                </div>

                <div>
                    <label for="source_url" class="block text-sm font-medium">
                        Source URL <span class="font-normal text-neutral-500">(optional)</span>
                    </label>
                    <input
                        id="source_url"
                        v-model="form.source_url"
                        type="url"
                        placeholder="https://supplier.example.com/item/1234"
                        class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/20"
                    />
                    <p v-if="fieldErrors.source_url" class="mt-1 text-sm text-red-600">
                        {{ fieldErrors.source_url[0] }}
                    </p>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium">
                        Notes <span class="font-normal text-neutral-500">(optional)</span>
                    </label>
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        rows="3"
                        placeholder="Materials, dimensions, who it's for — anything the copy should mention."
                        class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm outline-none focus:border-brand focus:ring-2 focus:ring-brand/20"
                    ></textarea>
                    <p v-if="fieldErrors.notes" class="mt-1 text-sm text-red-600">{{ fieldErrors.notes[0] }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="saving"
                    class="rounded-md bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-dark disabled:opacity-50"
                >
                    {{ saving ? 'Adding…' : 'Add product' }}
                </button>
            </form>
        </section>

        <section>
            <h2 class="text-sm font-semibold tracking-tight uppercase text-neutral-500">Products</h2>

            <p v-if="store.error" class="mt-3 rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ store.error }}
            </p>

            <p v-if="store.loading" class="mt-3 text-sm text-neutral-500">Loading…</p>

            <p v-else-if="!store.products.length" class="mt-3 text-sm text-neutral-500">
                No products yet. Add one above to generate copy for it.
            </p>

            <ul v-else class="mt-3 divide-y divide-neutral-200 rounded-lg border border-neutral-200 bg-white">
                <li v-for="product in store.products" :key="product.id">
                    <RouterLink
                        :to="{ name: 'products.show', params: { id: product.id } }"
                        class="flex items-center justify-between px-4 py-3 hover:bg-neutral-50"
                    >
                        <span class="text-sm font-medium">{{ product.name }}</span>
                        <span class="text-xs text-neutral-500">
                            {{ product.generations.length }}
                            {{ product.generations.length === 1 ? 'generation' : 'generations' }}
                        </span>
                    </RouterLink>
                </li>
            </ul>
        </section>
    </div>
</template>
