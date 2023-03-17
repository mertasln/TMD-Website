import { component } from 'picoapp';

export default component((node, ctx) => {
  const html = document.querySelector('html');
  const body = document.querySelector('body');
  const root = document.getElementById('root');

  const cartCount = node.querySelector('.js-cart-count');
  const cartToggle = document.querySelector('.js-cart-drawer-toggle');

  const cartDropdown = document.getElementById('cartDropdown');
  const cartScroll = document.querySelector('.cart-wrap');
  const notifyClose = document.getElementById('notifyClose');
  const cartClose = document.getElementById('cartClose');

  const searchBox = document.getElementById('Search');

  // CART
  cartToggle.addEventListener('click', e => {
    if (cartDropdown.classList.contains('active')) {
    } else {
      e.preventDefault();
      cartToggle.classList.add('active');
      cartDropdown.classList.add('active');
      ctx.emit('cart:toggle', state => ({
        cartOpen: !state.cartOpen,
      }));
      html.classList.add('mobile-fixed');
    }
  });

  function getCartCountWithoutFees() {
    const allItems = ctx.getState().cart.items;
    let cartCountWithoutFees = 0;
    allItems.forEach(item => {
      if (item.product_type !== 'mw_hidden_cart_fee') {
        cartCountWithoutFees += item.quantity;
      }
    });
    return cartCountWithoutFees;
  }

  ctx.on('cart:updated', () => {
    cartCount.innerHTML = getCartCountWithoutFees();
  });

  cartCount.innerHTML = getCartCountWithoutFees();

  if (notifyClose) {
    notifyClose.addEventListener('click', () => {
      body.classList.remove('notify-active');
    });
  }

  cartClose.addEventListener('click', () => {
    cartToggle.classList.remove('active');
    cartDropdown.classList.remove('active');
    html.classList.remove('mobile-fixed');
  });
 
  // searchBox.addEventListener('input', e => {
  //   if (e.target.value === '') {
  //     root.classList.remove('search-open');
  //   } else {
  //     root.classList.add('search-open');
  //   }

  //   const searchResults = document.querySelector('.snize-ac-results');

  //   document.addEventListener(
  //     'click',
  //     () => {
  //       root.classList.remove('search-open');
  //     },
  //     false
  //   );
  //   if (searchResults) {
  //     searchResults.addEventListener(
  //       'click',
  //       ev => {
  //         ev.stopPropagation();
  //       },
  //       false
  //     );
  //   }
  // });

  // searchBox.addEventListener('focus', e => {
  //   if (e.target.value !== '') {
  //     root.classList.add('search-open');
  //   }
  // });
});
