<?php declare(strict_types=1); ?>
<header class="topbar">

  <div class="topbar-spacer"></div>

  <div class="topbar-search">
    <span class="topbar-search-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
      </svg>
    </span>
    <input
      type="search"
      class="topbar-search-input"
      placeholder="Hľadať tasky, projekty..."
      aria-label="Vyhľadávanie"
    />
  </div>

  <div class="topbar-actions">

    <button class="topbar-icon-btn" data-theme-toggle title="Prepnúť tému">
      <span data-theme-icon>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="4"/>
          <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
        </svg>
      </span>
    </button>

    <div class="topbar-avatar" title="Profil">JK</div>

  </div>

</header>
