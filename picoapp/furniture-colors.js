import { component } from 'picoapp';

export default component(({ node }) => {
  const allProducts = document.querySelectorAll('.product-furniture');

  function handleize(str) {
    return str
      .toLowerCase()
      .replace(/[^\w\u00C0-\u024f]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  if (allProducts) {
    allProducts.forEach(product => {
      const colorSelect = product.querySelector('.furniture-color-selector');
      const allColors = product.querySelectorAll('.swatch-element input');
      const allFabrics = product.querySelectorAll('.options-fabric input');
      const allFabricOptions = product.querySelectorAll('.furniture-options');

      allFabrics.forEach(fabric => {
        if (fabric.checked) {
          const fabricValue = fabric.value;

          const currentFabric = document.querySelector(`.${handleize(fabricValue)}-options`);

          currentFabric.classList.add('active');
        }

        fabric.addEventListener('click', () => {
          const fabricValue = fabric.value;

          const currentFabric = document.querySelector(`.${handleize(fabricValue)}-options`);

          allFabricOptions.forEach(option => {
            option.classList.remove('active');
          });
          currentFabric.classList.add('active');

          const nextColor = currentFabric.querySelector('.swatch-element:first-child input');
          nextColor.checked = 'checked';
          nextColor.dispatchEvent(new Event('change'));
        });
      });

      allColors.forEach(color => {
        color.addEventListener('change', () => {
          const colorValue = color.value;

          const colorOptions = colorSelect.getElementsByTagName('option');
          colorOptions.forEach(option => {
            if (option.value === colorValue) {
              option.selected = 'selected';
              colorSelect.dispatchEvent(new Event('change'));
            }
          });
        });
      });
    });
  } else {
    const allFabrics = document.querySelectorAll('.options-fabric input');
    const allFabricOptions = document.querySelectorAll('.furniture-options');
    const colorSelect = document.querySelector('.furniture-color-selector');
    const allColors = document.querySelectorAll('.swatch-element input');
    allFabrics.forEach(fabric => {
      if (fabric.checked) {
        const fabricValue = fabric.value;

        const currentFabric = document.querySelector(`.${handleize(fabricValue)}-options`);

        currentFabric.classList.add('active');
      }

      fabric.addEventListener('click', () => {
        const fabricValue = fabric.value;

        const currentFabric = document.querySelector(`.${handleize(fabricValue)}-options`);

        allFabricOptions.forEach(option => {
          option.classList.remove('active');
        });
        currentFabric.classList.add('active');

        const nextColor = currentFabric.querySelector('.swatch-element:first-child input');
        nextColor.checked = 'checked';
        nextColor.dispatchEvent(new Event('change'));
      });
    });

    allColors.forEach(color => {
      color.addEventListener('change', () => {
        const colorValue = color.value;

        const colorOptions = colorSelect.getElementsByTagName('option');
        colorOptions.forEach(option => {
          if (option.value === colorValue) {
            option.selected = 'selected';
            colorSelect.dispatchEvent(new Event('change'));
          }
        });
      });
    });
  }
});
