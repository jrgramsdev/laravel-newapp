import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { api } from '../api';

const POLL_INTERVAL_MS = 1200;
// Generation is queued work; if it hasn't landed inside this window something
// is wrong upstream (no worker running, a wedged provider call) and polling
// forever just hides that from the merchant.
const POLL_TIMEOUT_MS = 120_000;

export const useProductStore = defineStore('products', () => {
    const products = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const pendingIds = ref(new Set());

    const timers = new Map();

    const byId = computed(() => (id) => products.value.find((p) => p.id === id) ?? null);
    const isGenerating = computed(() => (id) => pendingIds.value.has(id));

    function upsert(product) {
        const index = products.value.findIndex((p) => p.id === product.id);
        if (index === -1) {
            products.value.unshift(product);
        } else {
            products.value[index] = product;
        }
    }

    function replaceGeneration(generation) {
        const product = products.value.find((p) => p.id === generation.product_id);
        if (!product) return;

        const index = product.generations.findIndex((g) => g.id === generation.id);
        if (index === -1) {
            product.generations.unshift(generation);
        } else {
            product.generations[index] = generation;
        }
    }

    async function loadProducts() {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await api.listProducts();
            products.value = data;
        } catch (e) {
            error.value = e.message;
        } finally {
            loading.value = false;
        }
    }

    async function loadProduct(id) {
        error.value = null;
        try {
            const { data } = await api.getProduct(id);
            upsert(data);
            // A generation still running when the page loads (a reload
            // mid-flight, or a second tab) has nothing polling it yet.
            data.generations.filter((g) => !g.is_complete).forEach((g) => poll(g.id));
            return data;
        } catch (e) {
            error.value = e.message;
            return null;
        }
    }

    async function createProduct(payload) {
        const { data } = await api.createProduct(payload);
        upsert(data);
        return data;
    }

    async function deleteProduct(id) {
        await api.deleteProduct(id);
        products.value = products.value.filter((p) => p.id !== id);
    }

    async function generate(productId, type) {
        error.value = null;
        try {
            const { data } = await api.createGeneration(productId, type);
            replaceGeneration(data);
            poll(data.id);
            return data;
        } catch (e) {
            error.value = e.message;
            return null;
        }
    }

    function poll(generationId) {
        if (timers.has(generationId)) return;

        pendingIds.value = new Set(pendingIds.value).add(generationId);
        const startedAt = Date.now();

        const tick = async () => {
            try {
                const { data } = await api.getGeneration(generationId);
                replaceGeneration(data);

                if (data.is_complete) {
                    stopPolling(generationId);
                    return;
                }

                if (Date.now() - startedAt > POLL_TIMEOUT_MS) {
                    error.value = 'Generation timed out. Is a queue worker running?';
                    stopPolling(generationId);
                    return;
                }
            } catch (e) {
                error.value = e.message;
                stopPolling(generationId);
                return;
            }

            timers.set(generationId, setTimeout(tick, POLL_INTERVAL_MS));
        };

        timers.set(generationId, setTimeout(tick, POLL_INTERVAL_MS));
    }

    function stopPolling(generationId) {
        clearTimeout(timers.get(generationId));
        timers.delete(generationId);

        const next = new Set(pendingIds.value);
        next.delete(generationId);
        pendingIds.value = next;
    }

    /** Clear every timer — components call this on unmount. */
    function stopAllPolling() {
        [...timers.keys()].forEach(stopPolling);
    }

    return {
        products,
        loading,
        error,
        byId,
        isGenerating,
        loadProducts,
        loadProduct,
        createProduct,
        deleteProduct,
        generate,
        stopAllPolling,
    };
});
