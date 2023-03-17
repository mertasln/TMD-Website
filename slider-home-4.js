import { component } from 'picoapp';
import { Swiper, Navigation, Scrollbar } from 'swiper';
export default component(({ node }) => {
  Swiper.use([Navigation, Navigation, Scrollbar]);
  const slider = new Swiper('.slider-four', {
    loop: false,
    freeMode: true,
    slidesPerView: 1,
    spaceBetween: 18,
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
      nextEl: '.swiper-button-next-unique',
      prevEl: '.swiper-button-prev-unique'
    },
    breakpoints: {
      300: {
        slidesPerView: 2,
        spaceBetween: 9,
      },
      575: {
        slidesPerView: 3,
        spaceBetween: 9,
      },
      1200: {
        slidesPerView: 4,
        spaceBetween: 18,
      },
    },
  });
});