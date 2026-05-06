// Nastavi temu pred prvym renderom (zabrani blikaniu).
(function () {
  var saved = localStorage.getItem('ft-theme') || 'dark';
  document.documentElement.setAttribute('data-theme', saved);
  document.body && document.body.setAttribute('data-theme', saved);
})();

document.addEventListener('DOMContentLoaded', function () {

  // Aplikuje ulozenu temu na body (pre pripad ked body este nebol pri IIFE).
  var saved = localStorage.getItem('ft-theme') || 'dark';
  document.body.setAttribute('data-theme', saved);
  updateThemeIcon(saved);

  // Theme toggle.
  var themeBtn = document.querySelector('[data-theme-toggle]');
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      var current = document.body.getAttribute('data-theme') || 'dark';
      var next = current === 'dark' ? 'light' : 'dark';
      document.body.setAttribute('data-theme', next);
      localStorage.setItem('ft-theme', next);
      updateThemeIcon(next);
    });
  }

  // Topbar search — live client-side filter across pages.
  var searchInput = document.querySelector('.topbar-search-input');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      var q = this.value.toLowerCase().trim();

      // Board: kanban cards.
      var cards = document.querySelectorAll('.kanban-card[data-search-text]');
      if (cards.length) {
        cards.forEach(function (card) {
          card.hidden = q !== '' && !card.dataset.searchText.includes(q);
        });
        document.querySelectorAll('.kanban-col').forEach(function (col) {
          var count = col.querySelectorAll('.kanban-card[data-search-text]:not([hidden])').length;
          var badge = col.querySelector('.kanban-col-count');
          if (badge) badge.textContent = count;
        });
      }

      // Projects: project cards.
      document.querySelectorAll('.project-card[data-search-text]').forEach(function (card) {
        card.hidden = q !== '' && !card.dataset.searchText.includes(q);
      });

      // Time: table rows.
      document.querySelectorAll('.data-table tbody tr[data-search-text]').forEach(function (row) {
        row.hidden = q !== '' && !row.dataset.searchText.includes(q);
      });

      // Activity: timeline items + hide empty day groups.
      var items = document.querySelectorAll('.timeline-item[data-search-text]');
      if (items.length) {
        items.forEach(function (item) {
          item.hidden = q !== '' && !item.dataset.searchText.includes(q);
        });
        document.querySelectorAll('.timeline-date-group').forEach(function (group) {
          group.hidden = group.querySelector('.timeline-item[data-search-text]:not([hidden])') === null;
        });
      }
    });
  }

  function updateThemeIcon(theme) {
    var iconEl = document.querySelector('[data-theme-icon]');
    if (!iconEl) return;
    if (theme === 'dark') {
      // Zobraz ikonu slnka (prepnut na svetly rezim).
      iconEl.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>';
    } else {
      // Zobraz ikonu mesiaca (prepnut na tmavy rezim).
      iconEl.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
    }
  }

});
