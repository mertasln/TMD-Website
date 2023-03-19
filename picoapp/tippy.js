import { component } from 'picoapp';

export default component(({ node }) => {
  tippy('#syte-camera-header', {
    content: '<h4>Image Search</h4><p>Take a photo or upload an image to find similar items.</p><a href="javascript:void(0)" class="--syte-start-camera-upload" data-camera-button-placement="tooltip">START</a>',
    placement: 'bottom',
    theme: 'mcgee',
    allowHTML: true,
    interactive: true,
    maxWidth: 228,
    // hideOnClick: false,
    // trigger: 'click',
  }); 
});