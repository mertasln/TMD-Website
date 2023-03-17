import { component } from 'picoapp';
 
export default component((node, ctx) => {
  const lineID = node.querySelector('.cart-quantity-wrap').getAttribute('data-line');
  const productID = node.querySelector('.cart-quantity-wrap').getAttribute('data-id');
  const counterWrap = document.querySelector(`.cart-quantity-wrap[data-id="${productID}"]`);
  const decrease = counterWrap.querySelector('.js-counter-remove');
  const increase = counterWrap.querySelector('.js-counter-add');
  const quantity = counterWrap.querySelector('.js-counter-quantity');

  const min = parseInt(quantity.attributes.min.value);
  const max = parseInt(quantity.attributes.max.value);

  let count = parseInt(quantity.value);

  console.log(counterWrap);

  const set = i => {
    count = Math.max(min, Math.min(i, max || 10000));
    quantity.value = count;
  };

  const refresh = () => {
    document.getElementsByClassName('template-cart')[0].submit();
  };

  decrease.addEventListener('click', e => {
    e.preventDefault();
    if (count <= 1) {
      window.location.href = `/cart/change?line=${lineID}&amp;quantity=0`;
    } else {
      set(--count);
      setTimeout(function() {
        refresh();
      }, 100);
    }
  });

  increase.addEventListener('click', e => {
    e.preventDefault();
    set(++count);
    setTimeout(function() {
      refresh();
    }, 100);
  });
});
