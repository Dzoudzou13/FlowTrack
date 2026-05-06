<?php

declare(strict_types=1);

$pageTitle   = $pageTitle   ?? 'Settings | FlowTrack';
$styles      = ['css/app.css'];
$scripts     = ['js/app.js'];
$currentUser = $currentUser ?? auth_user() ?? [];
$workspace   = $workspace   ?? [];
$memberCount = $memberCount ?? 1;

require template_path('partials/header.php');

$words    = explode(' ', trim($currentUser['name'] ?? ''));
$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

?>
<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <div class="page-header">
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">Správa účtu a preferencií</p>
      </div>

      <div class="settings-layout">

        <!-- Settings nav -->
        <nav class="settings-nav">
          <a href="#profile" class="settings-nav-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
            Profil
          </a>
          <a href="#security" class="settings-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
            Bezpečnosť
          </a>
          <a href="#appearance" class="settings-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z"/>
            </svg>
            Vzhľad
          </a>
          <a href="#workspace" class="settings-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
            </svg>
            Workspace
          </a>
        </nav>

        <!-- Settings content -->
        <div>

          <!-- Profile -->
          <div class="form-card" id="profile" style="margin-bottom:20px;">
            <p class="form-section-title">Profil</p>
            <form method="POST" action="<?= htmlspecialchars(app_url('/settings/profile'), ENT_QUOTES, 'UTF-8') ?>">

              <div class="form-group" style="display:flex;align-items:center;gap:16px;">
                <div style="width:56px;height:56px;border-radius:50%;background:var(--accent);color:#fff;font-size:20px;font-weight:700;display:grid;place-items:center;flex-shrink:0;text-transform:uppercase;">
                  <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div>
                  <div style="font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:3px;"><?= htmlspecialchars($currentUser['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                  <div style="font-size:12px;color:var(--text-muted);">Avatar sa generuje z iniciálok mena</div>
                </div>
              </div>

              <hr class="divider" />

              <div class="form-group">
                <label class="form-label" for="name">Celé meno</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($currentUser['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
              </div>

              <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($currentUser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-primary">Uložiť profil</button>
              </div>
            </form>
          </div>

          <!-- Security -->
          <div class="form-card" id="security" style="margin-bottom:20px;">
            <p class="form-section-title">Zmena hesla</p>
            <form method="POST" action="<?= htmlspecialchars(app_url('/settings/password'), ENT_QUOTES, 'UTF-8') ?>">
              <div class="form-group">
                <label class="form-label" for="current_password">Aktuálne heslo</label>
                <input type="password" id="current_password" name="current_password" class="form-control" placeholder="••••••••" />
              </div>
              <div class="form-group">
                <label class="form-label" for="new_password">Nové heslo</label>
                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="••••••••" />
                <p class="form-hint">Minimálne 8 znakov.</p>
              </div>
              <div class="form-group">
                <label class="form-label" for="confirm_password">Potvrdiť nové heslo</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="••••••••" />
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary">Zmeniť heslo</button>
              </div>
            </form>
          </div>

          <!-- Appearance -->
          <div class="form-card" id="appearance" style="margin-bottom:20px;">
            <p class="form-section-title">Vzhľad</p>
            <div class="form-group">
              <label class="form-label">Farebná téma</label>
              <div style="display:flex;gap:12px;margin-top:4px;">
                <label style="cursor:pointer;">
                  <input type="radio" name="theme" value="dark" style="display:none;" id="themeDark" />
                  <div id="themeDarkCard" class="theme-option-card" style="border:2px solid var(--accent);border-radius:var(--radius-lg);padding:14px 20px;background:#111318;min-width:120px;text-align:center;transition:border-color 0.15s;">
                    <div style="font-size:20px;margin-bottom:6px;">🌙</div>
                    <div style="font-size:12.5px;font-weight:600;color:#eeeef0;">Tmavá</div>
                  </div>
                </label>
                <label style="cursor:pointer;">
                  <input type="radio" name="theme" value="light" style="display:none;" id="themeLight" />
                  <div id="themeLightCard" class="theme-option-card" style="border:2px solid var(--border);border-radius:var(--radius-lg);padding:14px 20px;background:#ffffff;min-width:120px;text-align:center;transition:border-color 0.15s;">
                    <div style="font-size:20px;margin-bottom:6px;">☀️</div>
                    <div style="font-size:12.5px;font-weight:600;color:#09090b;">Svetlá</div>
                  </div>
                </label>
              </div>
              <p class="form-hint" style="margin-top:8px;">Téma sa ukladá lokálne v prehliadači.</p>
            </div>
          </div>

          <!-- Workspace -->
          <div class="form-card" id="workspace">
            <p class="form-section-title">Workspace</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
              <div>
                <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:5px;">Názov workspace</div>
                <div style="font-size:14px;font-weight:600;color:var(--text-primary);"><?= htmlspecialchars($workspace['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
              </div>
              <div>
                <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:5px;">Slug</div>
                <div style="font-size:14px;color:var(--text-secondary);font-family:monospace;"><?= htmlspecialchars($workspace['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
              </div>
              <div>
                <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:5px;">Rola</div>
                <span class="role-badge <?= $currentUser['role'] ?? 'user' ?>"><?= ucfirst($currentUser['role'] ?? 'user') ?></span>
              </div>
              <div>
                <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:5px;">Členov</div>
                <div style="font-size:14px;font-weight:600;color:var(--text-primary);"><?= $memberCount ?></div>
              </div>
            </div>
          </div>

        </div><!-- /.settings-content -->
      </div><!-- /.settings-layout -->

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<script>
  document.querySelectorAll('.settings-nav-item').forEach(function (item) {
    item.addEventListener('click', function (e) {
      document.querySelectorAll('.settings-nav-item').forEach(function (i) { i.classList.remove('active'); });
      item.classList.add('active');
    });
  });

  var currentTheme = localStorage.getItem('ft-theme') || 'dark';
  var darkCard  = document.getElementById('themeDarkCard');
  var lightCard = document.getElementById('themeLightCard');
  function updateThemeCards(theme) {
    darkCard.style.borderColor  = theme === 'dark' ? 'var(--accent)' : 'var(--border)';
    lightCard.style.borderColor = theme === 'light' ? '#6366f1' : 'var(--border)';
  }
  updateThemeCards(currentTheme);
  document.getElementById('themeDark').closest('label').addEventListener('click', function () {
    document.body.setAttribute('data-theme', 'dark');
    localStorage.setItem('ft-theme', 'dark');
    updateThemeCards('dark');
  });
  document.getElementById('themeLight').closest('label').addEventListener('click', function () {
    document.body.setAttribute('data-theme', 'light');
    localStorage.setItem('ft-theme', 'light');
    updateThemeCards('light');
  });
</script>

<?php require template_path('partials/footer.php'); ?>
