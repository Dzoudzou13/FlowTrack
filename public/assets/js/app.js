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

  initTopbarSearch();

  function initTopbarSearch() {
    var input = document.querySelector('.topbar-search-input');
    if (!input) return;

    var selectors = [
      '.stat-card',
      '.project-card',
      '.kanban-card',
      '.task-list-item',
      '.time-entry-item',
      '.activity-item',
      '.timeline-item',
      '.data-table tbody tr'
    ];
    var items = Array.prototype.slice.call(document.querySelectorAll(selectors.join(',')))
      .filter(function (item) {
        return !item.closest('.topbar') && !item.querySelector('[colspan]');
      });

    if (items.length === 0) return;

    var empty = document.createElement('div');
    empty.className = 'global-search-empty';
    empty.textContent = 'Nenašli sa žiadne výsledky.';
    empty.hidden = true;
    var content = document.querySelector('.app-content');
    if (content) {
      content.appendChild(empty);
    }

    items.forEach(function (item) {
      item.setAttribute('data-global-search-text', normalizeText(
        item.getAttribute('data-search-text') || item.textContent || ''
      ));
    });

    input.addEventListener('input', function () {
      var query = normalizeText(input.value);
      var terms = query.split(' ').filter(Boolean);
      var visible = 0;

      items.forEach(function (item) {
        var haystack = item.getAttribute('data-global-search-text') || '';
        var matched = terms.length === 0 || terms.every(function (term) {
          return fuzzyIncludes(haystack, term);
        });

        item.classList.toggle('is-search-hidden', !matched);
        if (matched) visible++;
      });

      if (empty) {
        empty.hidden = terms.length === 0 || visible > 0;
      }
    });
  }

  function normalizeText(value) {
    return String(value || '')
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, ' ')
      .trim();
  }

  function fuzzyIncludes(haystack, term) {
    if (haystack.indexOf(term) !== -1) {
      return true;
    }

    var compactHaystack = haystack.replace(/\s+/g, '');
    var compactTerm = term.replace(/\s+/g, '');
    var index = 0;

    for (var i = 0; i < compactHaystack.length && index < compactTerm.length; i++) {
      if (compactHaystack[i] === compactTerm[index]) {
        index++;
      }
    }

    return compactTerm.length > 2 && index === compactTerm.length;
  }

});
