<?php

declare(strict_types=1);

$activeTab = $activeTab ?? 'login';
$isLogin = $activeTab === 'login';
$introKicker = $isLogin ? 'Vitaj späť' : 'Začni s FlowTrack';
$introText = $isLogin
    ? 'Prihlás sa do svojho pracovného priestoru a pokračuj v projektovej práci.'
    : 'Vytvor si účet a založ si vlastný workspace pre svoj tím a projekty.';
?>
<div class="page-glow glow-a"></div>
<div class="page-glow glow-b"></div>
<div class="page-grid"></div>

<main class="auth-shell auth-shell-compact">
  <section class="auth-stack">
    <div class="auth-topbar">
      <a class="brand" href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>">
        <span class="brand-mark">FT</span>
        <span class="brand-copy">
          <strong><?= htmlspecialchars(config('app.name', 'FlowTrack'), ENT_QUOTES, 'UTF-8') ?></strong>
          <small>Platforma pre riadenie projektov</small>
        </span>
      </a>

      <button class="theme-toggle" id="theme-toggle" type="button" aria-label="Prepnúť tému">
        <span class="theme-toggle-track">
          <span class="theme-toggle-thumb"></span>
        </span>
        <span class="theme-toggle-label">Tmavý režim</span>
      </button>
    </div>

    <div class="auth-card auth-card-standalone">
      <div class="auth-card-intro">
        <p class="auth-kicker"><?= htmlspecialchars($introKicker, ENT_QUOTES, 'UTF-8') ?></p>
        <h1 class="auth-title"><?= $isLogin ? 'Prihlásenie' : 'Registrácia' ?></h1>
        <p class="auth-subcopy"><?= htmlspecialchars($introText, ENT_QUOTES, 'UTF-8') ?></p>
      </div>

      <div class="auth-tabs" role="tablist" aria-label="Prihlásenie a registrácia">
        <a
          class="auth-tab <?= $isLogin ? 'is-active' : '' ?>"
          href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>"
          role="tab"
          aria-selected="<?= $isLogin ? 'true' : 'false' ?>"
        >
          Prihlásenie
        </a>
        <a
          class="auth-tab <?= ! $isLogin ? 'is-active' : '' ?>"
          href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>"
          role="tab"
          aria-selected="<?= ! $isLogin ? 'true' : 'false' ?>"
        >
          Registrácia
        </a>
      </div>

      <div class="alert" id="form-alert" role="status" aria-live="polite" hidden></div>

      <?php if (! empty($authError ?? null)): ?>
      <div class="alert is-error" role="alert">
        <?= htmlspecialchars($authError, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

      <section class="auth-form-panel is-active">
        <?php if ($isLogin): ?>
          <form
            class="auth-form"
            id="login-form"
            method="POST"
            action="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>"
            novalidate
          >
            <label class="field">
              <span>Email</span>
              <input
                type="email"
                name="email"
                placeholder=" admin@flowtrack.sk"
                autocomplete="email"
                required
              />
              <small class="field-error"></small>
            </label>

            <label class="field">
              <span>Heslo</span>
              <span class="password-field">
                <input
                  type="password"
                  name="password"
                  placeholder="Zadaj heslo"
                  autocomplete="current-password"
                  required
                />
                <button class="password-toggle" type="button" aria-label="Zobraziť heslo">
                  Zobraziť
                </button>
              </span>
              <small class="field-error"></small>
            </label>

            <div class="form-row">
              <label class="checkbox">
                <input type="checkbox" name="remember_me" />
                <span>Zapamätať si ma</span>
              </label>
              <a href="#" class="text-link">Zabudnuté heslo?</a>
            </div>

            <button class="primary-button" type="submit">Prihlásiť sa</button>
          </form>
        <?php else: ?>
          <form class="auth-form" id="register-form" method="POST" action="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
            <label class="field">
              <span>Meno a priezvisko</span>
              <input
                type="text"
                name="name"
                placeholder="Jozef Mrkvička"
                autocomplete="name"
                required
              />
              <small class="field-error"></small>
            </label>

            <label class="field">
              <span>Názov workspace</span>
              <input
                type="text"
                name="workspace"
                placeholder="Pixel Forge Team"
                required
              />
              <small class="field-error"></small>
            </label>

            <label class="field">
              <span>Email</span>
              <input
                type="email"
                name="email"
                placeholder=" jozef@flowtrack.sk"
                autocomplete="email"
                required
              />
              <small class="field-error"></small>
            </label>

            <div class="field-grid">
              <label class="field">
                <span>Heslo</span>
                <span class="password-field">
                  <input
                    type="password"
                    name="password"
                    placeholder="Min. 8 znakov"
                    autocomplete="new-password"
                    required
                  />
                  <button class="password-toggle" type="button" aria-label="Zobraziť heslo">
                    Zobraziť
                  </button>
                </span>
                <small class="field-error"></small>
              </label>

              <label class="field">
                <span>Potvrď heslo</span>
                <input
                  type="password"
                  name="confirmPassword"
                  placeholder="Zopakuj heslo"
                  autocomplete="new-password"
                  required
                />
                <small class="field-error"></small>
              </label>
            </div>

            <label class="checkbox checkbox-stacked">
              <input type="checkbox" name="terms" required />
              <span>Súhlasím so spracovaním údajov potrebných na vytvorenie účtu a workspace.</span>
            </label>

            <button class="primary-button" type="submit">Vytvoriť účet</button>
          </form>
        <?php endif; ?>
      </section>

      <p class="auth-note">
        Potrebuješ pomoc? Kontaktuj správcu workspace alebo použi obnovenie prístupu.
      </p>
    </div>
  </section>
</main>
