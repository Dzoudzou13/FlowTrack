<?php

declare(strict_types=1);

// Aktualna cesta pre zvyraznenie aktivnej polozky.
$currentPath = current_route_path();

function sidebarActive(string $path, string $current): string
{
    return $path === $current ? ' active' : '';
}

?>
<aside class="sidebar">

  <div class="sidebar-brand">
    <div class="sidebar-brand-mark">FT</div>
    <span class="sidebar-brand-name">FlowTrack</span>
  </div>

  <nav class="sidebar-nav">

    <div class="sidebar-section">
      <span class="sidebar-section-label">Hlavné</span>

      <a href="<?= htmlspecialchars(app_url('/dashboard'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-nav-item<?= sidebarActive('/dashboard', $currentPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
        </svg>
        Dashboard
      </a>

      <a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-nav-item<?= sidebarActive('/projects', $currentPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
        </svg>
        Projects
      </a>

      <a href="<?= htmlspecialchars(app_url('/board'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-nav-item<?= sidebarActive('/board', $currentPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
        </svg>
        Board
      </a>

      <a href="<?= htmlspecialchars(app_url('/time'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-nav-item<?= sidebarActive('/time', $currentPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Time Tracking
      </a>

    </div>

    <div class="sidebar-section">
      <span class="sidebar-section-label">Admin</span>

      <a href="<?= htmlspecialchars(app_url('/team'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-nav-item<?= sidebarActive('/team', $currentPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
        Team
      </a>

      <a href="<?= htmlspecialchars(app_url('/billing'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-nav-item<?= sidebarActive('/billing', $currentPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
        </svg>
        Billing
      </a>

      <a href="<?= htmlspecialchars(app_url('/activity'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-nav-item<?= sidebarActive('/activity', $currentPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
        </svg>
        Activity Log
      </a>

    </div>

    <div class="sidebar-section">
      <span class="sidebar-section-label">Účet</span>

      <a href="<?= htmlspecialchars(app_url('/settings'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-nav-item<?= sidebarActive('/settings', $currentPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
          <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Settings
      </a>

    </div>

  </nav>

  <div class="sidebar-user">
    <div class="sidebar-user-card">
      <div class="sidebar-avatar">JK</div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name">Jozef Kušnierik</div>
        <div class="sidebar-user-role">admin</div>
      </div>
      <a href="<?= htmlspecialchars(app_url('/logout'), ENT_QUOTES, 'UTF-8') ?>" class="sidebar-logout-btn" title="Odhlásiť sa">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
        </svg>
      </a>
    </div>
  </div>

</aside>
