import axios from 'axios';
export const setCountCard = (value:string)=>{
    let el = document.getElementById('count_product_in_cart');
    if(el)
    {
        el.classList.remove('d-none');
        el.innerHTML = value + '';
    }
}

export function LoadSales(loadingSales:any, sales: any)
{
    loadingSales.value = true;
    axios.post('/api/showSales').then(res=>{
        Object.assign(sales, (res.data as any).sales);
        loadingSales.value = false;
    })
}