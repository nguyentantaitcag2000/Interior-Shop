export const setCountCard = (value:string)=>{
    let el = document.getElementById('count_product_in_cart');
    if(el)
    {
        el.classList.remove('d-none');
        el.innerHTML = value + '';
    }
}