import Dashboard from './components/Dashboard.vue';
import Product from './pages/product/ListProduct.vue';
import Category from './pages/category/ListCategory.vue';
import Shop from './components/Shop.vue';
import ShopProductInfo from './pages/product/ShopProductInfo.vue';
import SignIn from './pages/auth/Signin.vue';
import SignUp from './pages/auth/Signup.vue';
import Cart from './pages/product/Cart.vue';
import CartProcessing from './pages/product/CartProcessing.vue';
import Checkout from './pages/product/Checkout.vue';
import Bill from './pages/product/Bill.vue';
import BillManager from './pages/product/BillManager.vue';
export default [  
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
        meta: { title: 'Daskboard' }

    },
    {
        path: '/admin/product',
        name: 'admin.product',
        component: Product,
        meta: { title: 'Sản phẩm' }
    },
    {
        path: '/admin/category',
        name: 'admin.category',
        component: Category,
        meta: { title: 'Danh mục' }

    },
    {
        path: '/admin/order/processing',
        name: 'admin.order.processing',
        component: BillManager,
        meta: { title: 'Đơn đặt hàng' }
    },

    {
        path: '/',
        name: 'shop',
        component: Shop,
        meta: { title: 'Cửa hàng' }

    },
    {
        path: '/product/:id',
        name: 'shop-product-info',
        component: ShopProductInfo,
        meta: { title: 'Thông tin sản phẩm' }

    }
    ,
    {
        path: '/auth/signin',
        name: 'signin',
        component: SignIn,
        meta: { title: 'Đăng nhập' }

    }
    ,
    {
        path: '/auth/signup',
        name: 'signup',
        component: SignUp,
        meta: { title: 'Đăng kí' }

    }
    ,
    {
        path: '/cart',
        name: 'cart',
        component: Cart,
        meta: { title: 'Giỏ hàng' }

    }
    ,
    {
        path: '/cart/processing',
        name: 'cart.processing',
        component: CartProcessing,
        meta: { title: 'Giỏ hàng đang xử lý' }

    }
    ,
    {
        path: '/checkout/:id?/:amount?',
        name: 'checkout',
        component: Checkout,
        meta: { title: 'Thanh toán' }
    }
    ,
    {
        path: '/bill/:id',
        name: 'bill',
        component: Bill,
        meta: { title: 'Chi tiết đơn hàng' }
    }
    
]