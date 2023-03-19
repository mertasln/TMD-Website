import { component } from 'picoapp';
import Swiper, { Autoplay, Pagination } from 'swiper';

export default component(({ node }) => {
  Swiper.use([Autoplay, Pagination]);
  const slider = new Swiper('.slider-quote', {
    pagination: {
      el: ".swiper-pagination",
      type: 'bullets',
      clickable: true,
    },
    loop: true,
    spaceBetween: 40,
    slidesPerView: 1,
    freeMode: true,
    freeModeMomentumBounce: false,
    speed: 800,
    parallax: true,
    freeModeMomentumRatio: 0.1,
    freeModeMomentumVelocityRatio: 0.1,
    freeModeSticky: true,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
  });
});
