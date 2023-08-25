import Dashboard from './components/Dashboard.vue';
import Product from './pages/product/ListProduct.vue';
import Category from './pages/category/ListCategory.vue';
export default [  
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
    },
    {
        path: '/admin/product',
        name: 'admin.product',
        component: Product,
    },
    {
        path: '/admin/category',
        name: 'admin.category',
        component: Category,
    },
]