import Dashboard from './components/Dashboard.vue';
import Product from './pages/product/ListProduct.vue';
import Category from './pages/category/ListCategory.vue';
import Shop from './components/Shop.vue';
import ShopProductInfo from './pages/product/ShopProductInfo.vue';
import SignIn from './pages/auth/Signin.vue';
import SignUp from './pages/auth/Signup.vue';
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
    {
        path: '/',
        name: 'shop',
        component: Shop,
    },
    {
        path: '/product/:id',
        name: 'shop-product-info',
        component: ShopProductInfo,
    }
    ,
    {
        path: '/auth/signin',
        name: 'signin',
        component: SignIn,
    }
    ,
    {
        path: '/auth/signup',
        name: 'signup',
        component: SignUp,
    }
]