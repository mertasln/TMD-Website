import { component } from 'picoapp';
import { addVariant } from '@/lib/cart.js';

export default component((node, ctx) => {
  const json = JSON.parse(node.querySelector('.js-product-json').innerHTML);
  const form = node.querySelector('form');
  const color_options = document.querySelector('.color-selectbox');
  const selected_color_label = document.querySelector('.selected-color');
  const { selectedOrFirstAvailableVariant, product } = json;
  let currentVariant = product.variants.filter(v => v.id === selectedOrFirstAvailableVariant)[0];

  try {
    document.addEventListener('DOMContentLoaded', function() {
      selected_color_label.innerHTML = color_options.value;
    });
    color_options.addEventListener('change', e => {
      selected_color_label.innerHTML = color_options.value;
    });
  } catch { 
    // NO Color Select
  }

  form.addEventListener('submit', e => {
    e.preventDefault();
    currentVariant = product.variants.filter(v => v.id === parseInt(form.elements.id.value))[0];
    const html_properties = node.getElementsByClassName('properties');
    const properties = {};
    for (let x = 0; x < html_properties.length; x++) {
      properties[html_properties[x].id] = html_properties[x].value;
    }
    console.log('ATTEMPTING ADD', currentVariant, form.elements.quantity.value, properties);
    addVariant(currentVariant, form.elements.quantity.value, properties);
  });
});
