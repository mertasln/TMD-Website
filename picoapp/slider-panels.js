import { component } from 'picoapp';
import { Swiper, Navigation, Scrollbar } from 'swiper';

export default component(({ node }) => {
  Swiper.use([Navigation, Navigation, Scrollbar]);
  const slider = new Swiper('.slider-panels', {
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
      hide: false,
      draggable: true,
      dragSize: 100,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      1200: {
        slidesPerView: 3,
        spaceBetween: 0,
      },
    },
  });
});
