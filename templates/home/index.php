<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'FlowTrack – Project Management & Time Tracking';
$styles    = ['css/landing.css'];
$scripts   = [];

require template_path('partials/header.php');

?>
<div class="lp-grid" aria-hidden="true"></div>

<!-- ====================================================
     NAV
     ==================================================== -->
<nav class="lp-nav" id="lpNav">
  <div class="lp-nav-inner">
    <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>" class="lp-brand">
      <div class="lp-brand-mark">FT</div>
      <span class="lp-brand-name">FlowTrack</span>
    </a>

    <ul class="lp-nav-links">
      <li><a href="#features">Funkcie</a></li>
      <li><a href="#how-it-works">Ako to funguje</a></li>
      <li><a href="#cta">Začať</a></li>
    </ul>

    <div class="lp-nav-actions">
      <button class="lp-nav-theme-btn" id="lpThemeBtn" title="Prepnúť tému" aria-label="Prepnúť farebnú tému">
        <svg id="lpThemeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
        </svg>
      </button>
      <a href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>" class="lp-btn-ghost">Prihlásiť sa</a>
      <a href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>" class="lp-btn-accent">Začať zadarmo</a>
    </div>
  </div>
</nav>

<!-- ====================================================
     HERO
     ==================================================== -->
<section class="lp-hero">
  <div class="lp-container">
    <div class="lp-hero-inner">

      <!-- Left: copy -->
      <div>
        <div class="lp-hero-badge">
          <span class="lp-hero-badge-dot"></span>
          Projekty · Kanban · Time Tracking
        </div>

        <h1>Riaď tímy.<br><em>Dodávaj projekty.</em><br>Bez chaosu.</h1>

        <p class="lp-hero-sub">
          FlowTrack spája projektový manažment, kanban board a sledovanie času na jednej platforme – pre tímy, ktorým záleží na výsledkoch.
        </p>

        <div class="lp-hero-ctas">
          <a href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>" class="lp-hero-cta-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
            Začať zadarmo
          </a>
          <a href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>" class="lp-hero-cta-secondary">Prihlásiť sa</a>
        </div>

        <div class="lp-hero-stats">
          <div class="lp-hero-stat">
            <strong>100%</strong>
            <span>Open source</span>
          </div>
          <div class="lp-hero-stat-div"></div>
          <div class="lp-hero-stat">
            <strong>4 moduly</strong>
            <span>V jednej platforme</span>
          </div>
          <div class="lp-hero-stat-div"></div>
          <div class="lp-hero-stat">
            <strong>Real-time</strong>
            <span>Activity tracking</span>
          </div>
        </div>
      </div>

      <!-- Right: app mockup -->
      <div class="lp-hero-preview">
        <div class="lp-preview-float">
          <div class="lp-preview-float-icon">✓</div>
          <div>
            <div class="lp-preview-float-label">Splnené dnes</div>
            <div>6 taskov</div>
          </div>
        </div>

        <div class="lp-preview-card">
          <div class="lp-preview-topbar">
            <div class="lp-preview-dot"></div>
            <div class="lp-preview-dot"></div>
            <div class="lp-preview-dot"></div>
            <span class="lp-preview-tab-title">FlowTrack · Board</span>
          </div>

          <div class="lp-preview-cols">
            <div class="lp-preview-col col-backlog">
              <div class="lp-preview-col-hd"><div class="lp-preview-col-dot"></div>Backlog</div>
              <div class="lp-preview-task">
                <div class="lp-task-tag tag-high">High</div>
                <div>API integrácia</div>
              </div>
              <div class="lp-preview-task">
                <div class="lp-task-tag tag-low">Low</div>
                <div>Dokumentácia</div>
              </div>
            </div>

            <div class="lp-preview-col col-progress">
              <div class="lp-preview-col-hd"><div class="lp-preview-col-dot"></div>In Progress</div>
              <div class="lp-preview-task">
                <div class="lp-task-tag tag-medium">Medium</div>
                <div>Dashboard UI</div>
              </div>
              <div class="lp-preview-task">
                <div class="lp-task-tag tag-high">High</div>
                <div>Auth modul</div>
              </div>
            </div>

            <div class="lp-preview-col col-done">
              <div class="lp-preview-col-hd"><div class="lp-preview-col-dot"></div>Done</div>
              <div class="lp-preview-task">
                <div class="lp-task-tag tag-low">Low</div>
                <div>DB schema</div>
              </div>
              <div class="lp-preview-task">
                <div class="lp-task-tag tag-medium">Medium</div>
                <div>Router setup</div>
              </div>
            </div>
          </div>

          <div class="lp-preview-metrics">
            <div class="lp-preview-metric">
              <span>Billable hodiny</span>
              <strong style="color:var(--accent);">124h</strong>
            </div>
            <div class="lp-preview-metric">
              <span>Revenue</span>
              <strong>6 200 €</strong>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<hr class="lp-divider">

<!-- ====================================================
     FEATURES
     ==================================================== -->
<section class="lp-section" id="features">
  <div class="lp-container">
    <div class="lp-section-hd">
      <div class="lp-kicker">Funkcie</div>
      <h2>Všetko, čo váš tím potrebuje</h2>
      <p>Od prvého tasku až po fakturáciu – FlowTrack pokrýva celý workflow bez nutnosti ďalších nástrojov.</p>
    </div>

    <div class="lp-features-grid">

      <div class="lp-feature-card">
        <div class="lp-feature-icon fi-green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3.75 6A2.25 2.25 0 016 3.75h12A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6z"/>
            <path d="M12 9v6m-3-3h6"/>
          </svg>
        </div>
        <h3>Projektový manažment</h3>
        <p>Vytváraj projekty, sleduj progres a priradzuj tasky správnym ľuďom v tíme.</p>
        <ul class="lp-feature-bullets">
          <li>Priradenie tasku konkrétnym členom</li>
          <li>Deadline a priorita pre každý task</li>
          <li>Progress bar podľa splnených taskov</li>
        </ul>
      </div>

      <div class="lp-feature-card">
        <div class="lp-feature-icon fi-purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
          </svg>
        </div>
        <h3>Kanban Board</h3>
        <p>Vizuálne board pre prehľadné sledovanie stavu taskov naprieč všetkými projektmi.</p>
        <ul class="lp-feature-bullets">
          <li>4 stĺpce: Backlog, In Progress, Review, Done</li>
          <li>Filter podľa projektu a priority</li>
          <li>Deadline a overdue upozornenia</li>
        </ul>
      </div>

      <div class="lp-feature-card">
        <div class="lp-feature-icon fi-orange">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h3>Time Tracking</h3>
        <p>Zaznamenávaj čas strávený na každom tasku a generuj prehľady pre celý tím.</p>
        <ul class="lp-feature-bullets">
          <li>Billable a non-billable záznamy</li>
          <li>Týždenný a mesačný prehľad</li>
          <li>Filtrovanie podľa projektu a mesiaca</li>
        </ul>
      </div>

      <div class="lp-feature-card">
        <div class="lp-feature-icon fi-teal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <h3>Billing &amp; Revenue</h3>
        <p>Automaticky vypočítavaj revenue z hodinových sadzieb a exportuj mesačné prehľady.</p>
        <ul class="lp-feature-bullets">
          <li>Hodinová sadzba nastaviteľná per projekt</li>
          <li>Mesačný billing report</li>
          <li>Billable vs. non-billable štatistiky</li>
        </ul>
      </div>

    </div>
  </div>
</section>

<hr class="lp-divider">

<!-- ====================================================
     HOW IT WORKS
     ==================================================== -->
<section class="lp-section" id="how-it-works">
  <div class="lp-container">
    <div class="lp-section-hd">
      <div class="lp-kicker">Ako to funguje</div>
      <h2>Tri kroky k plynulému tímu</h2>
      <p>Začni do piatich minút – bez zbytočnej konfigurácie a bez kreditnej karty.</p>
    </div>

    <div class="lp-steps">
      <div class="lp-step">
        <div class="lp-step-number">1</div>
        <h3>Vytvor workspace</h3>
        <p>Zaregistruj sa, pomenuj workspace a si pripravený. Automaticky sa staneš adminom svojho tímu.</p>
      </div>
      <div class="lp-step">
        <div class="lp-step-number">2</div>
        <h3>Pridaj projekty a tím</h3>
        <p>Vytvor prvý projekt, nastav hodinovú sadzbu a pozvi kolegov emailom. Roly Admin a User.</p>
      </div>
      <div class="lp-step">
        <div class="lp-step-number">3</div>
        <h3>Sleduj pokrok &amp; fakturuj</h3>
        <p>Tasky na kanbane, čas v time trackeri, revenue v billing prehľade. Všetko na jednom mieste.</p>
      </div>
    </div>
  </div>
</section>

<!-- ====================================================
     CTA BANNER
     ==================================================== -->
<div class="lp-cta-wrap" id="cta">
  <div class="lp-container">
    <div class="lp-cta-inner">
      <div class="lp-cta-glow" aria-hidden="true"></div>
      <h2>Začni <em>zadarmo</em> ešte dnes</h2>
      <p>Vytvor si účet za 30 sekúnd. Žiadna kreditná karta. Žiadne skryté poplatky.</p>
      <div class="lp-cta-btns">
        <a href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>" class="lp-hero-cta-primary" style="font-size:.96rem;padding:15px 32px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
          Vytvoriť účet zadarmo
        </a>
        <a href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>" class="lp-hero-cta-secondary" style="font-size:.96rem;padding:15px 26px;">Prihlásiť sa</a>
      </div>
    </div>
  </div>
</div>

<!-- ====================================================
     FOOTER
     ==================================================== -->
<footer class="lp-footer">
  <div class="lp-container">
    <div class="lp-footer-inner">
      <a href="<?= htmlspecialchars(app_url('/'), ENT_QUOTES, 'UTF-8') ?>" class="lp-brand">
        <div class="lp-brand-mark">FT</div>
        <span class="lp-brand-name">FlowTrack</span>
      </a>
      <ul class="lp-footer-links">
        <li><a href="#features">Funkcie</a></li>
        <li><a href="#how-it-works">Ako to funguje</a></li>
        <li><a href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>">Prihlásiť sa</a></li>
        <li><a href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>">Registrácia</a></li>
      </ul>
      <span class="lp-footer-copy">© <?= date('Y') ?> FlowTrack. Všetky práva vyhradené.</span>
    </div>
  </div>
</footer>

<?php require template_path('partials/cookie-bar.php'); ?>

<script>
// ── Nav scroll effect ──────────────────────────────────
var lpNav = document.getElementById('lpNav');
window.addEventListener('scroll', function () {
  lpNav.classList.toggle('is-scrolled', window.scrollY > 24);
}, { passive: true });
if (window.scrollY > 24) lpNav.classList.add('is-scrolled');

// ── Theme toggle ───────────────────────────────────────
var themeBtn  = document.getElementById('lpThemeBtn');
var themeIcon = document.getElementById('lpThemeIcon');
var MOON = '<path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>';
var SUN  = '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32l1.41-1.41"/>';

function setTheme(t) {
  document.documentElement.setAttribute('data-theme', t);
  document.body.setAttribute('data-theme', t);
  localStorage.setItem('ft-theme', t);
  themeIcon.innerHTML = t === 'dark' ? MOON : SUN;
}

var saved = localStorage.getItem('ft-theme') || 'dark';
setTheme(saved);

themeBtn.addEventListener('click', function () {
  var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  setTheme(next);
});

// ── Smooth anchor scroll ───────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(function (a) {
  a.addEventListener('click', function (e) {
    var target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
</script>

<?php require template_path('partials/footer.php'); ?>
