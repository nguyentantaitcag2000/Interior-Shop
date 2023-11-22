import {ref} from 'vue';

export const cart_tabs = ref([
    {
        label: 'Giỏ hàng',
        icon: 'fas fa-cart-plus',
        to: '/cart'
    },
    {
        label: 'Đang xử lý',
        icon: 'fas fa-snowboarding',
        to: '/cart/processing'
    },
    {
        label: 'Đã xử lý',
        icon: 'fas fa-check',
        to: '/cart/done'
    }
    ,
    {
        label: 'Đang vận chuyển',
        icon: 'fas fa-running',
        to: '/cart/shipping'
    },
    {
        label: 'Đã bị huỷ',
        icon: 'fas fa-minus',
        to: '/cart/destroy'
    }
]);