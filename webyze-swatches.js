import { component } from 'picoapp';

export default component(({ node }) => {
  const urlContainer = document.querySelectorAll('.webyze-url');
  if (urlContainer) {
    urlContainer.forEach(container => {
      const url = container.getAttribute('data-url');

      const script = document.createElement('script');
      script.async = true;
      script.src = url;

      container.appendChild(script);
    });
  }
});
