import { component } from 'picoapp';
import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';

export default component((node, ctx) => {
  const sidebar = document.getElementById('shopify-section-sidebar');
  const body = document.querySelector('body');

  const navShop = node.querySelector('.nav-shop');
  const navLooks = node.querySelector('.nav-looks');
  const navLooksMobile = node.querySelector('.nav-looks-mobile');
  const navMore = node.querySelector('.nav-more');
  const navWrap = node.querySelector('.nav-wrap');
  const panelBG = node.querySelector('.nav-panel-bg');
  const navOverlay = node.querySelector('.nav-overlay');

  const navToggles = node.querySelectorAll('.nav-top-level');

  const mobileToggle = document.getElementById('mobileToggle');

  const header = document.getElementById('header');

  const moreBG = document.getElementById('subMenuBG');
  const dropdownToggle = document.getElementById('subMenuToggle');
  const dropdown = document.querySelector('.sub-menu-dropdown');

  const accountBG = document.getElementById('accountMenuBG');
  const accountToggle = document.getElementById('accountMenuToggle');
  const accountDropdown = document.querySelector('.account-dropdown');

  const root = document.getElementById('root');
  // const searchBox = document.getElementById('Search');
  // const searchTrigger = document.getElementById('searchTrigger');
  // const searchClose = document.getElementById('searchClose');

  function getSiblings(item) {
    const siblings = [];
    let sibling = item.parentNode.firstChild;
    while (sibling) {
      if (sibling.nodeType === 1 && sibling !== item) {
        siblings.push(sibling);
      }
      sibling = sibling.nextSibling;
    }

    return siblings;
  }

  function openDropdown() {
    const bgHeight = dropdown.offsetHeight;
    moreBG.style.height = `${bgHeight}px`;
    header.classList.add('dropdown-open');
  }
  function closeDropdown() {
    header.classList.remove('dropdown-open');
  }

  function openAccount() {
    const bgHeight = accountDropdown.offsetHeight;
    accountBG.style.height = `${bgHeight}px`;
    header.classList.add('account-open');
  }
  function closeAccount() {
    header.classList.remove('account-open');
  }

  // function openMobileSearch() {
  //   root.classList.add('mob-search-open');
  //   searchBox.focus();
  // }
  // function closeMobileSearch() {
  //   root.classList.remove('mob-search-open');
  //   searchBox.blur();
  // }

  function openCollections(item) {
    sidebar.classList.add('collection-open');
    if (item) {
      item.classList.add('nav-level-active');
      navToggles.forEach(topLevel => {
        if (topLevel.classList.contains('nav-level-active')) {
          const subCategories = topLevel.querySelectorAll('.panel-col');
          const navBackSub = topLevel.querySelector('.mobile-sub-nav-back');
          const navBackCurrent = topLevel.querySelector('.mobile-sub-nav-current');
          navBackSub.classList.add('active');
          subCategories.forEach(subCategory => {
            if (subCategory.classList.contains('nav-level-active')) {
              const subTrigger = subCategory.querySelector('.cat');
              navBackCurrent.innerHTML = `<span class="sub-slash">/</span> <span>${subTrigger.innerHTML}</span>`;
            }
          });
        }
      });
    }
  }
  function closeCollections(item) {
    sidebar.classList.remove('collection-open');
    if (item) {
      item.classList.remove('nav-level-active');
    } else {
      navToggles.forEach(topLevel => {
        if (topLevel.classList.contains('nav-level-active')) {
          const subCategories = topLevel.querySelectorAll('.panel-col');
          const navBack = topLevel.querySelector('.mobile-sub-nav-back');
          const navBackCurrent = topLevel.querySelector('.mobile-sub-nav-current');
          subCategories.forEach(subCategory => {
            if (subCategory.classList.contains('nav-level-active')) {
              subCategory.classList.remove('nav-level-active');
            }
          });
          navBack.classList.remove('active');
          navBackCurrent.innerHTML = ``;
        }
      });
    }
  }

  function openSubcategories(item) {
    if (!sidebar.classList.contains('nav-open')) {
      sidebar.classList.add('nav-open');
    }
    if (item) {
      const panel = item.querySelector('.panel');
      const panelWidth = panel.offsetWidth;
      panelBG.style.width = `${panelWidth}px`;
      item.classList.add('nav-level-active');
      sidebar.classList.add('subcategory-open');
    }
  }
  function closeSubcategories(item) {
    if (item) {
      item.classList.remove('nav-level-active');
    } else {
      closeCollections();
      navToggles.forEach(topLevel => {
        if (topLevel.classList.contains('nav-level-active')) {
          topLevel.classList.remove('nav-level-active');
        }
      });
    }
    closeCollections();
    sidebar.classList.remove('subcategory-open');
  }

  function openNav() {
    if (!sidebar.classList.contains('nav-open')) {
      sidebar.classList.add('nav-open');
      mobileToggle.classList.add('active');
      disableBodyScroll(sidebar);
    }
    if (header.classList.contains('dropdown-open')) {
      closeDropdown();
    }
    if (root.classList.contains('mob-search-open')) {
      closeMobileSearch();
    }
  }
  function closeNav() {
    closeSubcategories();
    sidebar.classList.remove('nav-open');
    mobileToggle.classList.remove('active');
    panelBG.style.width = '14.5rem';
    clearAllBodyScrollLocks();
  }

  // Dropdown triggered
  dropdownToggle.addEventListener('click', e => {
    e.preventDefault();
    if (header.classList.contains('dropdown-open')) {
      closeDropdown();
    } else {
      openDropdown();
      closeNav();
    }
  });

  // Account triggered
  if (accountToggle) {
    accountToggle.addEventListener('click', e => {
      e.preventDefault();
      if (header.classList.contains('account-open')) {
        closeAccount();
      } else {
        openAccount();
        closeNav();
      }
    });
  }

  // Category triggered
  navToggles.forEach(topLevel => {
    const trigger = topLevel.querySelector('.nav-trigger');
    const subCategories = topLevel.querySelectorAll('.panel-col');
    const navBackSub = topLevel.querySelector('.mobile-sub-nav-back');

    trigger.addEventListener('click', e => {
      e.preventDefault();
      if (topLevel.classList.contains('nav-level-active')) {
        if (window.innerWidth > 1200) {
          closeNav();
        }
      } else {
        const siblings = getSiblings(topLevel);
        siblings.forEach(sibling => {
          if (sibling.classList.contains('nav-level-active')) {
            closeSubcategories(sibling);
            closeCollections();
          }
        });
        openNav();
        openSubcategories(topLevel);
      }
    });

    // Subcategory triggered
    subCategories.forEach(subCategory => {
      const subTrigger = subCategory.querySelector('.cat');
      const allLinks = subCategory.querySelectorAll('li a');

      subTrigger.addEventListener('click', () => {
        const siblings = getSiblings(subCategory);
        siblings.forEach(sibling => {
          if (sibling.classList.contains('nav-level-active')) {
            closeCollections(sibling);
          }
        });
        openCollections(subCategory, topLevel, subTrigger);
      });

      allLinks.forEach(category => {
        category.addEventListener('click', () => {
          setTimeout(() => {
            closeNav();
          }, 400);
        });
      });
    });

    // Back triggered
    navBackSub.addEventListener('click', () => {
      if (navBackSub.classList.contains('active')) {
        closeCollections();
      } else {
        closeSubcategories();
      }
    });
  });

  // Mobile Nav Triggered
  mobileToggle.addEventListener('click', e => {
    e.preventDefault();
    if (sidebar.classList.contains('nav-open')) {
      closeNav();
    } else {
      openNav();
    }
  });

  // Overlay triggered
  navOverlay.addEventListener('click', () => {
    closeNav();
  });

  // Tabs triggered
  const tabShop = document.getElementById('tabShop');
  const tabLooks = document.getElementById('tabLooks');
  const tabMore = document.getElementById('tabMore');

  tabShop.addEventListener('click', () => {
    navShop.classList.add('active');
    navLooks.classList.remove('active');
    navLooksMobile.classList.remove('active');
    navMore.classList.remove('active');
    tabShop.classList.add('active');
    tabLooks.classList.remove('active');
    tabMore.classList.remove('active');
  });

  tabLooks.addEventListener('click', () => {
    closeSubcategories();
    navShop.classList.remove('active');
    navLooks.classList.remove('active');
    navLooksMobile.classList.add('active');
    navMore.classList.remove('active');
    tabShop.classList.remove('active');
    tabLooks.classList.add('active');
    tabMore.classList.remove('active');
  });

  tabMore.addEventListener('click', () => {
    closeSubcategories();
    navShop.classList.remove('active');
    navLooks.classList.remove('active');
    navLooksMobile.classList.remove('active');
    navMore.classList.add('active');
    tabShop.classList.remove('active');
    tabLooks.classList.remove('active');
    tabMore.classList.add('active');
  });

  function onResize() {
    navToggles.forEach(topLevel => {
      if (topLevel.classList.contains('nav-level-active')) {
        const panel = topLevel.querySelector('.panel');
        const panelWidth = panel.offsetWidth;
        panelBG.style.width = `${panelWidth}px`;
      }
    });

    if (!node.querySelectorAll('.nav-top-level.nav-level-active').length) {
      if (window.innerWidth > 1200) {
        closeNav();
      }
    }
  }

  // searchTrigger.addEventListener('click', e => {
  //   if (root.classList.contains('mob-search-open')) {
  //     closeMobileSearch();
  //   } else {
  //     openMobileSearch();
  //     closeNav();
  //   }
  // });

  // searchClose.addEventListener('click', e => {
  //   if (root.classList.contains('mob-search-open')) {
  //     closeMobileSearch(); 
  //   }
  // });

  window.addEventListener('resize', onResize);
});
