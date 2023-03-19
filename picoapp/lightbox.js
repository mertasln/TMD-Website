import { component } from 'picoapp';
import * as basicLightbox from 'basiclightbox';

export default component(({ node }) => {
  const popupLinks = document.querySelectorAll('.popup-link');

  popupLinks.forEach(popupLink => {
    popupLink.addEventListener('click', (event) => {
      event.preventDefault();
      const target = popupLink.getAttribute('data-target');
      const content = document.getElementById(target);
      const content_clone = content.cloneNode(true);
      content_clone.id = target+'1';
      const openPopup = basicLightbox.create(content_clone);
      openPopup.show(); 
      const popupClose = document.querySelectorAll('.popup-close');
      popupClose.forEach(popupLink => {
        popupLink.addEventListener('click', (event) => {
          openPopup.close();
        });
      });
    });
  });
});