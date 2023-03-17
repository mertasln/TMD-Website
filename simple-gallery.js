import { component } from 'picoapp';

export default component(({ node }) => {
      var next = document.getElementById('nextSlide');
      var prev = document.getElementById('prevSlide');
      var selected = document.getElementById('first');

      next.addEventListener('click', nextSlide);
      prev.addEventListener('click', prevSlide);

      function nextSlide() {
        fadeOut(selected);
        selected = document.getElementById(selected.getAttribute('data-next-link'));
        next.getElementsByTagName('span')[0].innerHTML = selected.getAttribute('data-next');
        prev.getElementsByTagName('span')[0].innerHTML = selected.getAttribute('data-prev');
        fadeIn(selected);
      }
      function prevSlide() {
        fadeOut(selected);
        selected = document.getElementById(selected.getAttribute('data-prev-link'));
        next.getElementsByTagName('span')[0].innerHTML = selected.getAttribute('data-next');
        prev.getElementsByTagName('span')[0].innerHTML = selected.getAttribute('data-prev');
        fadeIn(selected);
      }
      // ** FADE OUT FUNCTION **
      function fadeOut(el) {
        el.style.opacity = 1;
        (function fade() {
            if ((el.style.opacity -= .1) < 0) {
                el.style.display = "none";
            } else {
                requestAnimationFrame(fade);
            }
        })();
      };

      // ** FADE IN FUNCTION **
      function fadeIn(el, display) {
          el.style.opacity = 0;
          el.style.display = display || "block";
          (function fade() {
              var val = parseFloat(el.style.opacity);
              if (!((val += .1) > 1)) {
                  el.style.opacity = val;
                  requestAnimationFrame(fade);
              }
          })();
      };

  // setInterval(function() {
  //   for (var i = 0; i < slides.length; i++) {
  //     slides[i].style.opacity = 0;
  //   }
  //   current = (current != slides.length - 1) ? current + 1 : 0;
  //   slides[current].style.opacity = 1;
  // }, 3000);
});