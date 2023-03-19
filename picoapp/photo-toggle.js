import { component } from 'picoapp';

export default component(({ node }) => {
  const allPhotoToggles = document.querySelectorAll('.photo-toggle');

  allPhotoToggles.forEach(toggle => {
    const navDefault = toggle.querySelector('.photo-toggle-nav .default');
    const navAlt = toggle.querySelector('.photo-toggle-nav .alt');
    const photoDefault = toggle.querySelector('.photo-toggle-wrap .default');
    const photoAlt = toggle.querySelector('.photo-toggle-wrap .alt');

    navDefault.addEventListener('click', e => {
      e.preventDefault();

      if (navDefault.classList.contains('active')) {
        // nothing
      } else {
        navDefault.classList.add('active');
        navAlt.classList.remove('active');
        photoDefault.classList.add('active');
        photoAlt.classList.remove('active');
      }
    });

    navAlt.addEventListener('click', e => {
      e.preventDefault();

      if (navAlt.classList.contains('active')) {
        // nothing
      } else {
        navDefault.classList.remove('active');
        navAlt.classList.add('active');
        photoDefault.classList.remove('active');
        photoAlt.classList.add('active');
      }
    });
  });
});
