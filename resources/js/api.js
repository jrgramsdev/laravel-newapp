const BASE = '/api/v1';

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

async function request(method, path, body) {
    const response = await fetch(`${BASE}${path}`, {
        method,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: body === undefined ? undefined : JSON.stringify(body),
    });

    if (response.status === 204) {
        return null;
    }

    const payload = await response.json().catch(() => null);

    if (!response.ok) {
        // 422 carries per-field messages; anything else gets a single message
        // so callers have one shape to render.
        throw {
            status: response.status,
            message: payload?.message ?? `Request failed (${response.status})`,
            errors: payload?.errors ?? {},
        };
    }

    return payload;
}

export const api = {
    listProducts: () => request('GET', '/products'),
    createProduct: (product) => request('POST', '/products', product),
    getProduct: (id) => request('GET', `/products/${id}`),
    deleteProduct: (id) => request('DELETE', `/products/${id}`),
    createGeneration: (productId, type) =>
        request('POST', `/products/${productId}/generations`, { type }),
    getGeneration: (id) => request('GET', `/generations/${id}`),
};
