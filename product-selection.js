import { component } from 'picoapp';
import options from '@/lib/options.js';
import getProductJson from '@/lib/getProductJson.js';
import Swiper, { Controller, Pagination } from 'swiper';
import { formatMoney } from '@/lib/currency.js';
import { disableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';

export default component(({ node }) => {
  const multipleProducts = document.querySelectorAll('.product-images-slider');
  let productSlider = null;
  let thumbSlider = null;
  Swiper.use([Controller, Pagination]);

  if (multipleProducts.length > 1) {
    for (let i = 0; i < multipleProducts.length; i++) {
      productSlider = new Swiper(multipleProducts[i], {
        spaceBetween: 2,
      });
    }
  } else {
    thumbSlider = new Swiper('.gallery-slider', {
      pagination: {
        el: '.swiper-pagination',
        type: 'bullets',
        clickable: true,
      },
      spaceBetween: 1,
      slidesPerView: 5,
      slideToClickedSlide: true,
      loop: false,
      centeredSlides: true,
      watchOverflow: true,
      breakpoints: {
        575: {
          slidesPerView: 8,
        },
      },
    });

    productSlider = new Swiper('.product-images-slider', {
      spaceBetween: 2,
      slidesPerView: 1,
      loop: false,
      watchOverflow: true,
    });

    thumbSlider.controller.control = productSlider;
    productSlider.controller.control = thumbSlider;
  }

  const zoomOverlay = document.createElement('div');
  const zoomOverlayClose = document.createElement('div');
  const allPhotos = document.querySelectorAll('.product-image');
  const closeBtn =
    '<svg viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg"><path d="M7.5 8.91421L13.2929 14.7071L14.7071 13.2929L8.91421 7.5L14.7071 1.70711L13.2929 0.292892L7.5 6.08579L1.70711 0.292892L0.292892 1.70711L6.08579 7.5L0.292892 13.2929L1.70711 14.7071L7.5 8.91421Z"/></svg>';
  zoomOverlayClose.innerHTML = closeBtn;

  zoomOverlay.classList.add('zoom-overlay');
  zoomOverlayClose.classList.add('zoom-overlay-close');

  function createZoom(target) {
    const scrollID = target.getAttribute('data-image-zoom-id');
    allPhotos.forEach(photoWrap => {
      const newPhotoWrap = document.createElement('div');
      const newPhoto = document.createElement('img');

      const photo = photoWrap.querySelector('img');
      const photoID = photoWrap.getAttribute('data-image-zoom-id');
      const photoURL = photo.getAttribute('data-zoom');
      newPhoto.src = photoURL;
      newPhotoWrap.appendChild(newPhoto);
      newPhotoWrap.classList.add('zoom-overlay-photo');
      newPhotoWrap.setAttribute('id', photoID);
      zoomOverlay.appendChild(newPhotoWrap);

      document.querySelector('body').appendChild(zoomOverlay);
    });
    zoomOverlay.appendChild(zoomOverlayClose);
    disableBodyScroll(zoomOverlay);
    setTimeout(function() {
      document.getElementById(scrollID).scrollIntoView(true);
    }, 100);
  }

  function killZoom() {
    zoomOverlay.innerHTML = '';
    zoomOverlay.remove();
    clearAllBodyScrollLocks();
  }

  allPhotos.forEach(photoWrap => {
    photoWrap.addEventListener('click', () => {
      createZoom(photoWrap);
    });
  });

  if (zoomOverlayClose) {
    zoomOverlayClose.addEventListener('click', () => {
      killZoom();
    });
  }

  const main_option = document.querySelector('[data-option-main]');
  const opts = options(node);
  const price = document.querySelector('[data-price]');
  const sale_price = document.querySelector('[data-sale-price]');
  const backordered_wrapper = document.querySelector('.backordered-date');
  const backordered_date_display = document.querySelector('[data-backordered-date]');
  const backordered_json = document.querySelector('[data-product-backordered-json]');
  const backordered_data = JSON.parse(backordered_json.innerHTML);
  const quantity_max = document.getElementById('QuantityMax');
  const quantity_input = document.querySelector('.js-counter-quantity');
  const low_stock_tag = document.querySelectorAll('.low-stock');
  const for_sale_wrapper = document.querySelector('.qty-fav-wrapper');
  const email_when_available = document.getElementById('email-when-avail');
  const checkout_button = document.querySelector('.btn-shop');
  const coming_soon = document.querySelector('.coming-soon');
  const sku = document.querySelector('[data-product-sku]');
  const email_form_variant_id = document.querySelector('[data-variant-id]');
  const show_buying = document.getElementsByClassName('buying-block');

  let max_inventory = 0;
  // const affirm_price = document.querySelector('.affirm-as-low-as');

  // cache
  getProductJson();

  opts.onUpdate(state => {
    getProductJson().then(json => {
      const variant = json.variants.filter(v => v.id == state.id)[0];
      console.log('Variant', variant);
      if (variant) {
        email_form_variant_id.value = variant.id;

        var opts = main_option.options;
        for (var opt, j = 0; opt = opts[j]; j++) {
          if (opt.value == variant.id) {
            main_option.selectedIndex = j;
            break;
          }
        }
        productSlider.slides.forEach((slide, position) => {
          const index = slide.getAttribute('data-image-id');
          if (index) {
            if (parseInt(variant.image_id) === parseInt(index)) {
              productSlider.slideTo(position);
            }
          }
        });

        const backordered_date = backordered_data.variants.filter(x => x.id == state.id)[0];
        if (backordered_date.backordered != '') {
          backordered_date_display.innerHTML = backordered_date.backordered;
          backordered_wrapper.style.display = 'block';
        } else {
          backordered_wrapper.style.display = 'none';
        }

        sku.innerHTML = variant.sku;
        const compare_price = variant.compare_at_price;
        const main_price = variant.price;

        if (
          compare_price != '' &&
          parseFloat(parseFloat(compare_price).toFixed(2)) >
            parseFloat(parseFloat(main_price).toFixed(2))
        ) {
          sale_price.innerHTML = formatMoney(main_price);
          sale_price.style.display = 'inline';
          price.innerHTML = `Reg ${formatMoney(compare_price)}`;
          price.classList.add('regular-price');
        } else {
          price.innerHTML = formatMoney(main_price);
          price.classList.remove('regular-price');
          sale_price.innerHTML = '';
          sale_price.style.display = 'none';
        }
        // updateAffirmPromos(variant.price)
        let variant_available = true;
        if (
          variant.inventory_management == 'shopify' &&
          variant.inventory_quantity <= 0 &&
          variant.inventory_policy != 'continue'
        ) {
          variant_available = false;
        }
        console.log('Available', variant_available);
        if (variant_available) {
          for_sale_wrapper.style.display = 'flex';
          email_when_available.style.display = 'none';
          checkout_button.innerHTML = 'Add to Cart';
          checkout_button.removeAttribute('disabled');
          checkout_button.classList.remove('inactive');
          for(var i = 0; i < show_buying.length; i++){
            show_buying[i].style.display = "block";
          }
        } else {
          for_sale_wrapper.style.display = 'none';
          email_when_available.style.display = 'block';
          for(var i = 0; i < show_buying.length; i++){
            show_buying[i].style.display = "none";
          }
          try {
            checkout_button.innerHTML = 'Out Of Stock';
            checkout_button.classList.add('inactive');
            checkout_button.setAttribute('disabled', 'disabled');
          } catch {
            console.log('NO CHECKOUT BUTTOn');
          }
        }
        if (coming_soon) {
          for_sale_wrapper.style.display = 'flex';
          email_when_available.style.display = 'block';
          for(var i = 0; i < show_buying.length; i++){
            show_buying[i].style.display = "none";
          }
        }
        if (variant.inventory_management == 'shopify' && variant.inventory_quantity < 21 && variant.inventory_quantity > 0) {
          for (let i = 0; i < low_stock_tag.length; i++) {
            low_stock_tag[i].style.display = "block";
          }
        } else {
          for (let i = 0; i < low_stock_tag.length; i++) {
            low_stock_tag[i].style.display = "none";
          }
        }

        if (variant.inventory_management == 'shopify' && variant.inventory_quantity > 0) {
          max_inventory = variant.inventory_quantity;
        } else if (
          variant.inventory_management == 'shopify' &&
          variant.inventory_quantity == 0 &&
          variant.inventory_policy != 'continue'
        ) {
          max_inventory = variant.inventory_quantity;
        } else {
          max_inventory = 1000000000000;
        }
        try {
          quantity_max.value = max_inventory;
          quantity_input.setAttribute('max', max_inventory);
        } catch {
          console.log('NO QTY');
        }
      } else {
        console.log('NO VARIANT');
        for_sale_wrapper.style.display = 'none';
        email_when_available.style.display = 'none';
        price.innerHTML = '';
        try {
          checkout_button.innerHTML = 'Out Of Stock';
          checkout_button.classList.add('inactive');
          checkout_button.setAttribute('disabled', 'disabled');
        } catch {
          console.log('NO CHECKOUT BUTTOn');
        }
      }
    });
  });
  function updateAffirmPromos(newPrice) {
    // Update the wrapper elements' attributes in the DOM
    console.log('Affirm Change');
    document.getElementById('affirmLearnMore').setAttribute('data-amount', newPrice);

    affirm.ui.ready(function() {
      affirm.ui.refresh();
    });
  }
});
