import { component } from 'picoapp';

export default component(({ node }) => {
  const allItems = document.querySelectorAll('.number-menu li');
  const mainPhoto = document.querySelector('.number-menu-image');
  const mainTitle = document.querySelector('.number-menu-title');
  const mainButton = document.querySelector('.number-menu-button');

  function setActive(index) {
    allItems.forEach(item => {
      item.classList.remove('active');
    });
    allItems[index].classList.add('active');
  }

  allItems.forEach(link => {
    link.addEventListener('mouseover', e => {
      const targetIndex = [].indexOf.call(allItems, link);
      setActive(targetIndex);
      mainPhoto.src = link.getAttribute('data-image');
      mainTitle.innerHTML = link.getAttribute('data-title');
      mainButton.setAttribute('href', link.getAttribute('data-button-link'));
      mainButton.innerHTML = '<span>'+link.getAttribute('data-button-text')+'</span>';
    });
  });
});
