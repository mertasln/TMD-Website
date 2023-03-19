import { component } from 'picoapp';
import { Swiper, Navigation, Scrollbar } from 'swiper';
export default component(({ node }) => {
  Swiper.use([Navigation, Navigation, Scrollbar]);
  const slider = new Swiper('.slider-four', {
    loop: false,
    freeMode: true,
    slidesPerView: 1,
    spaceBetween: 20,
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
      1200: {
        slidesPerView: 3,
        spaceBetween: 40,
      },
    },
  });
});