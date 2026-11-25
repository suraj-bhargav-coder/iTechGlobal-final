(function () {
  'use strict';

  document.body.classList.add('has-js');

  const menuButton = document.querySelector('.menu-btn');
  const navigation = document.getElementById('main-nav');

  if (menuButton && navigation) {
    const desktopNavigation = window.matchMedia('(min-width: 961px)');
    const setMenuOpen = (isOpen) => {
      navigation.classList.remove('open');
      navigation.classList.toggle('open', isOpen);
      menuButton.setAttribute('aria-expanded', String(isOpen));
      menuButton.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
      menuButton.querySelector('.menu-label').textContent = isOpen ? 'Close' : 'Menu';
    };

    menuButton.addEventListener('click', () => {
      setMenuOpen(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    navigation.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => setMenuOpen(false));
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && menuButton.getAttribute('aria-expanded') === 'true') {
        setMenuOpen(false);
        menuButton.focus();
      }
    });

    document.addEventListener('pointerdown', (event) => {
      if (
        menuButton.getAttribute('aria-expanded') === 'true' &&
        !navigation.contains(event.target) &&
        !menuButton.contains(event.target)
      ) {
        setMenuOpen(false);
      }
    });

    desktopNavigation.addEventListener('change', () => setMenuOpen(false));
  }

  const revealElements = document.querySelectorAll('.reveal');
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    revealElements.forEach((element) => element.classList.add('show'));
  } else {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    revealElements.forEach((element) => observer.observe(element));
  }

  document.querySelectorAll('.faq-item').forEach((item) => {
    const button = item.querySelector('.q');
    const answer = item.querySelector('.a');

    if (!button || !answer) return;
    answer.setAttribute('role', 'region');

    button.addEventListener('click', () => {
      const willOpen = !item.classList.contains('open');

      document.querySelectorAll('.faq-item.open').forEach((openItem) => {
        openItem.classList.remove('open');
        openItem.querySelector('.q')?.setAttribute('aria-expanded', 'false');
      });

      item.classList.toggle('open', willOpen);
      button.setAttribute('aria-expanded', String(willOpen));
    });
  });

  const topButton = document.querySelector('.top');
  if (topButton) {
    const updateTopButton = () => {
      topButton.style.display = window.scrollY > 320 ? 'grid' : 'none';
    };

    window.addEventListener('scroll', updateTopButton, { passive: true });
    topButton.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
    });
    updateTopButton();
  }

  const sections = Array.from(document.querySelectorAll('main section[id]'));
  const navLinks = Array.from(document.querySelectorAll('.nav-links a[href*="#"]'));

  if ('IntersectionObserver' in window && sections.length) {
    const sectionObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        navLinks.forEach((link) => {
          const isCurrent = link.hash === '#' + entry.target.id;
          link.classList.toggle('active', isCurrent);
          if (isCurrent) {
            link.setAttribute('aria-current', 'location');
          } else {
            link.removeAttribute('aria-current');
          }
        });
      });
    }, { rootMargin: '-35% 0px -55%', threshold: 0 });

    sections.forEach((section) => sectionObserver.observe(section));
  }
})();
