import { createRouter, createWebHistory } from 'vue-router';
import ProductList from './pages/ProductList.vue';
import ProductDetail from './pages/ProductDetail.vue';

const routes = [
    { path: '/', name: 'products.index', component: ProductList },
    {
        path: '/products/:id',
        name: 'products.show',
        component: ProductDetail,
        props: (route) => ({ id: Number(route.params.id) }),
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
