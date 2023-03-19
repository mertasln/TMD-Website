import { component } from 'picoapp';

export default component(({ node }) => {
  const imageMap = document.querySelector('.image-map-wrap');
  const dots = document.querySelectorAll('.hotspot');
  const sliderNav = document.querySelectorAll('.slider-card-nav li');
  const sliderProducts = document.querySelectorAll('.slider-card-product');

  function setActive(index) {
    dots.forEach(dot => {
      dot.classList.remove('active');
    });
    sliderNav.forEach(nav => {
      nav.classList.remove('active');
    });
    sliderProducts.forEach(product => {
      product.classList.remove('active');
    });

    dots[index].classList.add('active');
    sliderNav[index].classList.add('active');
    sliderProducts[index].classList.add('active');
  }

  dots.forEach(target => {
    target.addEventListener('click', e => {
      const targetIndex = [].indexOf.call(dots, target);
      setActive(targetIndex);
    });
  });

  sliderNav.forEach(target => {
    target.addEventListener('click', e => {
      const targetIndex = [].indexOf.call(sliderNav, target);
      setActive(targetIndex);
    });
  });
});
