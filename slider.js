import { component } from 'picoapp';
import { Swiper, Navigation, Scrollbar } from 'swiper';

export default component(({ node }) => {
  Swiper.use([Navigation, Scrollbar]);
  const slider = new Swiper('.slider-products', {
    preloadImages: false,
    lazy: true,
    loop: false,
    spaceBetween: 20,
    slidesPerView: 1,
    freeMode: true,
    freeModeMomentumBounce: false,
    speed: 800,
    parallax: true,
    freeModeMomentumRatio: 0.1,
    freeModeMomentumVelocityRatio: 0.1,
    freeModeSticky: true,
    scrollbar: {
      el: '.swiper-scrollbar',
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      300: {
        slidesPerView: 2,
      },
      575: {
        slidesPerView: 3,
      },
      1120: {
        slidesPerView: 4,
      },
    },
  });
});
