<?php
// Cookie consent bar — shown until user accepts or declines.
// State is stored in localStorage under 'ft-cookie-consent'.
?>
<div id="ft-cookie-bar" role="dialog" aria-label="Cookie súhlas" aria-live="polite" style="display:none;">
  <div class="cookie-bar-card">
    <div class="cookie-bar-text">
      <span class="cookie-bar-icon" aria-hidden="true">🍪</span>
      <div>
        <strong>Táto stránka používa cookies</strong>
        <span>Používame cookies na zlepšenie vášho zážitku a analýzu návštevnosti. Vaše rozhodnutie si pamätáme lokálne.</span>
      </div>
    </div>
    <div class="cookie-bar-actions">
      <button class="cookie-btn cookie-btn-decline" id="ftCookieDecline">Odmietnuť</button>
      <button class="cookie-btn cookie-btn-accept"  id="ftCookieAccept">Prijať všetky</button>
    </div>
  </div>
</div>

<style>
/* Cookie bar base styles — included here so the bar works on every page
   regardless of which CSS file is loaded. */
#ft-cookie-bar {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 9999;
  padding: 0 24px 20px;
  pointer-events: none;
}
#ft-cookie-bar .cookie-bar-card {
  max-width: 960px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: space-between;
  background: rgba(18, 38, 49, 0.97);
  border: 1px solid rgba(157, 191, 184, 0.30);
  border-radius: 20px;
  padding: 18px 24px;
  backdrop-filter: blur(22px);
  box-shadow: 0 8px 40px rgba(3, 11, 15, 0.42);
  pointer-events: all;
  font-family: "Manrope", "Segoe UI", sans-serif;
  color: #ecf3ef;
}
[data-theme="light"] #ft-cookie-bar .cookie-bar-card {
  background: rgba(255, 255, 255, 0.97);
  border-color: rgba(18, 48, 58, 0.15);
  box-shadow: 0 8px 32px rgba(42, 59, 53, 0.18);
  color: #12303a;
}
.cookie-bar-text {
  display: flex;
  align-items: flex-start;
  gap: 13px;
  flex: 1;
  min-width: 180px;
}
.cookie-bar-icon { font-size: 20px; line-height: 1; flex-shrink: 0; margin-top: 1px; }
.cookie-bar-text strong { display: block; font-size: 13.5px; font-weight: 700; margin-bottom: 3px; }
.cookie-bar-text span   { font-size: 12.5px; color: #9fb4af; line-height: 1.5; }
[data-theme="light"] .cookie-bar-text span { color: #60757b; }
.cookie-bar-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.cookie-btn {
  padding: 9px 20px;
  border-radius: 10px;
  font-family: inherit;
  font-size: 0.84rem;
  font-weight: 600;
  cursor: pointer;
  transition: opacity .15s;
  border: none;
}
.cookie-btn:hover { opacity: .82; }
.cookie-btn-decline {
  background: transparent;
  border: 1px solid rgba(157, 191, 184, 0.28);
  color: #9fb4af;
}
[data-theme="light"] .cookie-btn-decline { border-color: rgba(18,48,58,.18); color: #60757b; }
.cookie-btn-accept {
  background: linear-gradient(135deg, #6ee7c8 0%, #a7f3d0 100%);
  color: #062018;
  box-shadow: 0 4px 14px rgba(110, 231, 200, 0.28);
}
@media (max-width: 600px) {
  #ft-cookie-bar { padding: 0 12px 12px; }
  #ft-cookie-bar .cookie-bar-card { flex-direction: column; align-items: flex-start; gap: 14px; padding: 16px 18px; }
}
</style>

<script>
(function () {
  if (localStorage.getItem('ft-cookie-consent')) return;

  var bar     = document.getElementById('ft-cookie-bar');
  var accept  = document.getElementById('ftCookieAccept');
  var decline = document.getElementById('ftCookieDecline');

  bar.style.display = 'block';

  accept.addEventListener('click', function () {
    localStorage.setItem('ft-cookie-consent', 'accepted');
    bar.style.display = 'none';
  });

  decline.addEventListener('click', function () {
    localStorage.setItem('ft-cookie-consent', 'declined');
    bar.style.display = 'none';
  });
}());
</script>
