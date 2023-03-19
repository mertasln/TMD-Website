import { component } from 'picoapp';

export default component(({ node }) => {
  const allLinks = document.querySelectorAll('.link-menu a');
  const mainPhoto = document.querySelector('.display-image');

  function displayImage(src){
    mainPhoto.src = src;
  }

  allLinks.forEach(link => {
    link.addEventListener('mouseover', e => {
      console.log(link)
      displayImage(link.getAttribute('data-image'));
    });
  });
});
