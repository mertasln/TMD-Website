import { component } from 'picoapp';

export default component((node, ctx) => {
    const accept = node.querySelector('.custom_accept');
    const shop_btn = document.querySelector('.btn-shop');
    console.log('LOADED');
    
    accept.addEventListener('change', e => {
        console.log('CHECKED');

        if (accept.checked) {
            shop_btn.disabled = false;
            shop_btn.classList.add("js-add-to-cart");
            shop_btn.classList.remove("inactive");
        } else {
            shop_btn.disabled = true;
            shop_btn.classList.remove("js-add-to-cart");
            shop_btn.classList.add("inactive");
        }  
    });
});
