import { component } from 'picoapp';

export default component((node, ctx) => {
  const allFaqs = document.querySelectorAll('.faq');

  function collapse(element) {
    const detail = element.querySelector('.faq-detail');

    detail.style.height = `0`;
    element.classList.remove('active');
  }

  function expand(element) {
    const detail = element.querySelector('.faq-detail');
    const detailHeight = detail.scrollHeight;

    detail.style.height = `${detailHeight}px`;
    element.classList.add('active');
  }

  allFaqs.forEach(section => {
    const allItems = section.querySelectorAll('.faq-item');

    allItems.forEach(item => {
      const title = item.querySelector('h5');

      title.addEventListener('click', () => {
        if (item.classList.contains('active')) {
          collapse(item);
        } else {
          const getSiblings = function(item) {
            const siblings = [];
            let sibling = item.parentNode.firstChild;
            while (sibling) {
              if (sibling.nodeType === 1 && sibling !== item) {
                siblings.push(sibling);
              }
              sibling = sibling.nextSibling;
            }

            return siblings;
          };
          const collapsers = getSiblings(item);
          collapsers.forEach(collapser => {
            if (collapser.classList.contains('faq-item')) {
              collapse(collapser);
            }
          });
          expand(item);
        }
      });
    });
  });
});
