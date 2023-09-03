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
    }
]);