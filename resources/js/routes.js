
export default [  
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: ()=> import('./components/Dashboard.vue'),
        meta: { title: 'Daskboard' }

    },
    {
        path: '/admin/product',
        name: 'admin.product',
        component: ()=> import('./pages/product/ListProduct.vue'),
        meta: { title: 'Sản phẩm' }
    },
    {
        path: '/admin/category',
        name: 'admin.category',
        component: ()=> import('./pages/category/ListCategory.vue'),
        meta: { title: 'Danh mục' }

    },
    {
        path: '/admin/saleoff',
        name: 'admin.saleoff',
        component: ()=> import('./pages/saleoff/SaleOff.vue'),
        meta: { title: 'Sale Off' }

    },
    {
        path: '/admin/bill/:id',
        name: 'admin.bill',
        component: () => import('./pages/product/BillManager.vue'),
        meta: { title: 'Đơn đặt hàng' }
    },

    {
        path: '/',
        name: 'shop',
        component: () => import('./components/Shop.vue'),
        meta: { title: 'Cửa hàng' }

    },
    {
        path: '/product/:id',
        name: 'shop-product-info',
        component: () => import('./pages/product/ShopProductInfo.vue'),
        meta: { title: 'Thông tin sản phẩm' }

    }
    ,
    {
        path: '/auth/signin',
        name: 'signin',
        component: () => import('./pages/auth/Signin.vue'),
        meta: { title: 'Đăng nhập' }

    }
    ,
    {
        path: '/auth/signup',
        name: 'signup',
        component: () => import('./pages/auth/Signup.vue'),
        meta: { title: 'Đăng kí' }

    }
    ,
    {
        path: '/cart',
        name: 'cart',
        component: () => import('./pages/product/Cart.vue'),
        meta: { title: 'Giỏ hàng' }

    },
    {
        path: '/introduce',
        name: 'introduce',
        component: () => import('./pages/introduce.vue'),
        meta: { title: 'Giới thiệu' }

    },
    {
        path: '/contact',
        name: 'contact',
        component: () => import('./pages/contact.vue'),
        meta: { title: 'Liên hệ' }

    }
    ,
    {
        path: '/cart/processing',
        name: 'cart.processing',
        component: () => import('./pages/product/CartProcessing.vue'),
        meta: { title: 'Giỏ hàng đang xử lý' }

    }
    ,
    {
        path: '/checkout/:id?/:amount?/:idColor?/:idMaterial?/:idDimensions?',
        name: 'checkout',
        component: () => import('./pages/product/Checkout.vue'),
        meta: { title: 'Thanh toán' }
    }
    ,
    {
        path: '/bill/:id',
        name: 'bill',
        component: () => import('./pages/product/Bill.vue'),
        meta: { title: 'Chi tiết đơn hàng' }
    }
    
]