import { component } from 'picoapp';

export default component(({ node }) => {
  const templateContainer = document.querySelector('.template');
  const root = document.getElementById('root');

  if (templateContainer) {
    const templateName = templateContainer.getAttribute('data-template-name');
    if (templateContainer) {
      root.className = templateName;
    }
  } else {
    root.className = '';
  }
});
