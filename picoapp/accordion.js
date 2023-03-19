import { component } from 'picoapp';
// import { Accordion } from '@/lib/vanilla-js-accordion.js';


export default component(( node ) => {
  var Accordion = function(options) {
      var element = typeof options.element === 'string' ?
                      document.getElementById(options.element) : options.element,
          openTab = options.openTab,
          oneOpen = options.oneOpen || false,

          titleClass   = 'js-Accordion-title',
          contentClass = 'js-Accordion-content';

      render();

      /**
      * Initial rendering of the accordion.
      */
      function render() {
          // attach classes to buttons and containers
          [].forEach.call(element.querySelectorAll('button'),
              function(item) {
                  item.classList.add(titleClass);
                  item.nextElementSibling.classList.add(contentClass);
              });

          // attach only one click listener
          element.addEventListener('click', onClick);

          // accordion starts with all tabs closed
          closeAll();

          // sets the open tab - if defined
          if (openTab) {
              open(openTab);
          }
      }

      /**
      * Handles clicks on the accordion.
      *
      * @param {object} e - Element the click occured on.
      */
      function onClick(e) {
          if (e.target.className.indexOf(titleClass) === -1) {
              return;
          }

          var nextContent = e.target.nextElementSibling;
        
          if (nextContent.style.height !== '0px' && nextContent.style.height !== '') {
              e.target.nextElementSibling.style.height = 0;
              e.target.classList.remove('open');
              return;
          }
        

          if (oneOpen) {
              closeAll();
          }

          toggle(nextContent);
      }

      /**
      * Closes all accordion tabs.
      */
      function closeAll() {
          [].forEach.call(element.querySelectorAll('.' + contentClass), function(item) {
              item.style.height = 0;
          });
          [].forEach.call(element.querySelectorAll('.' + titleClass), function(item) {
              item.classList.remove('open');
          });
      }
 
      /**
      * Toggles corresponding tab for each title clicked.
      *
      * @param {object} el - The content tab to show or hide.
      */
      function toggle(el) {
          // getting the height every time in case
          // the content was updated dynamically
          var height = el.scrollHeight;

          if (el.style.height === '0px' || el.style.height === '') {
              el.style.height = height + 'px';
              el.previousElementSibling.classList.add('open');
          } else {
              el.style.height = 0;
              el.previousElementSibling.classList.remove('open');
          }
      }


    /**
      * Returns the corresponding accordion title element by index.
      *
      * @param {number} n - Index of tab to return
      */
      function getTargetTitle(n) {
          return element.querySelectorAll('.' + titleClass)[n - 1];
      }

      /**
      * Returns the corresponding accordion content element by index.
      *
      * @param {number} n - Index of tab to return
      */
      function getTarget(n) {
          return element.querySelectorAll('.' + contentClass)[n - 1];
      }

      /**
      * Opens a tab by index.
      *
      * @param {number} n - Index of tab to open.
      *
      * @public
      */
      function open(n) {
          var target = getTarget(n);
          var titleTarget = getTargetTitle(n);

          if (target) {
              if (oneOpen) closeAll();
              target.style.height = target.scrollHeight + 'px';
              titleTarget.classList.add('open');
          }
      }

      /**
      * Closes a tab by index.
      *
      * @param {number} n - Index of tab to close.
      *
      * @public
      */
      function close(n) {
          var target = getTarget(n);
          var titleTarget = getTargetTitle(n);

          if (target) {
              target.style.height = 0;
              titleTarget.classList.remove('open');
          }
      }

      /**
      * Destroys the accordion.
      *
      * @public
      */
      function destroy() {
          element.removeEventListener('click', onClick);
      }

      return {
          open: open,
          close: close,
          destroy: destroy
      };
  };
  var accordion = new Accordion({
    element: node.querySelector(".js-Accordion"),
    openTab: 1,
    oneOpen: true
  });
})