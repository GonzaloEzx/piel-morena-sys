(() => {
  const turnForm = document.querySelector('#pmPedirTurno');
  turnForm?.addEventListener('submit', event => {
    const selected = turnForm.querySelector('select').selectedOptions[0];
    if (!selected?.dataset.waUrl) return;
    event.preventDefault();
    window.open(selected.dataset.waUrl, '_blank', 'noopener');
  });
  const menu = document.querySelector('.pmv-menu');
  menu?.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => { menu.open = false; });
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && menu?.open) {
      menu.open = false;
      menu.querySelector('summary').focus();
    }
  });
  document.addEventListener('click', event => {
    if (menu?.open && !menu.contains(event.target)) menu.open = false;
  });

  // En móvil la acción fija acompaña el recorrido cuando no hay otra visible.
  const mobileBar = document.querySelector('.pmv-mobile-bar');
  const mainActions = [...document.querySelectorAll('main .pmv-button[href*="wa.me/"], #pmPedirTurno button')];
  if (mobileBar && mainActions.length && 'IntersectionObserver' in window) {
    const visibleActions = new Set();
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting && entry.intersectionRatio >= 0.5) visibleActions.add(entry.target);
        else visibleActions.delete(entry.target);
      });
      // No ocultar un control mientras está siendo utilizado con el teclado.
      if (!mobileBar.contains(document.activeElement)) mobileBar.hidden = visibleActions.size > 0;
    }, { rootMargin: '-80px 0px -72px 0px', threshold: 0.5 });
    mainActions.forEach(action => observer.observe(action));
    mobileBar.addEventListener('focusout', () => {
      queueMicrotask(() => {
        if (!mobileBar.contains(document.activeElement)) mobileBar.hidden = visibleActions.size > 0;
      });
    });
  }

  const search = document.querySelector('#pmv-search');
  const results = document.querySelector('#pmv-search-results');
  const empty = document.querySelector('#pmv-no-results');
  const groups = [...document.querySelectorAll('.pmv-catalog-group')];
  const previousOpen = new Map();
  const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
  search?.addEventListener('input', () => {
    const query = normalize(search.value);
    let count = 0;
    groups.forEach(group => {
      if (query && !previousOpen.has(group)) previousOpen.set(group, group.open);
      const items = [...group.querySelectorAll('[data-service]')];
      let visible = 0;
      items.forEach(item => {
        item.hidden = !normalize(item.dataset.service).includes(query);
        if (!item.hidden) visible++;
      });
      group.hidden = visible === 0;
      if (query && visible) group.open = true;
      if (!query && previousOpen.has(group)) {
        group.open = previousOpen.get(group);
        previousOpen.delete(group);
      }
      count += visible;
    });
    results.textContent = query ? `${count} ${count === 1 ? 'servicio encontrado' : 'servicios encontrados'}.` : '';
    empty.hidden = count !== 0;
  });
})();
