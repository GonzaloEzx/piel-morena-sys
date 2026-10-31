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
