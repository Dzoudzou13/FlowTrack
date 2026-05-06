<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Team | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

$members   = $members   ?? [];
$workspace = $workspace ?? [];

require template_path('partials/header.php');

?>
<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <div class="page-header">
        <div class="page-header-row">
          <div>
            <h1 class="page-title">Team</h1>
            <p class="page-subtitle">Členovia workspace · <?= count($members) ?> <?= count($members) === 1 ? 'člen' : 'členov' ?></p>
          </div>
          <button class="btn btn-primary" id="inviteBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
            </svg>
            Pozvať člena
          </button>
        </div>
      </div>

      <div id="inviteForm" style="display:none;margin-bottom:20px;">
        <div class="form-card" style="max-width:100%;">
          <p class="form-section-title">Pozvať nového člena</p>
          <form method="POST" action="<?= htmlspecialchars(app_url('/team/invite'), ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="invite_email">Email</label>
                <input type="email" id="invite_email" name="email" class="form-control" placeholder="meno@priklad.sk" required />
              </div>
              <div class="form-group">
                <label class="form-label" for="invite_role">Rola</label>
                <select id="invite_role" name="role" class="form-control">
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Pozvať</button>
              <button type="button" class="btn btn-ghost" id="cancelInvite">Zrušiť</button>
            </div>
          </form>
        </div>
      </div>

      <div class="member-grid">
        <?php foreach ($members as $m): ?>
          <?php
            $words    = explode(' ', trim($m['name']));
            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
          ?>
          <div class="member-card">
            <div class="member-card-avatar"><?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?></div>
            <div class="member-card-name"><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="member-card-email"><?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="member-card-footer">
              <span class="role-badge <?= $m['role'] ?>"><?= ucfirst($m['role']) ?></span>
              <span style="font-size:11.5px;color:var(--text-muted);">od <?= date('j.n.Y', strtotime($m['created_at'])) ?></span>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="member-card" style="border-style:dashed;opacity:0.5;">
          <div class="member-card-avatar" style="background:var(--surface-raised);color:var(--text-muted);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
            </svg>
          </div>
          <div class="member-card-name" style="color:var(--text-muted);">Pozvať člena</div>
          <div class="member-card-email">Pridaj ďalšieho do tímu</div>
          <div class="member-card-footer" style="border-top:none;justify-content:center;">
            <button class="btn btn-ghost btn-sm" id="inviteBtnInCard">Pozvať</button>
          </div>
        </div>
      </div>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<script>
  var inviteBtn = document.getElementById('inviteBtn');
  var inviteBtnInCard = document.getElementById('inviteBtnInCard');
  var inviteForm = document.getElementById('inviteForm');
  var cancelInvite = document.getElementById('cancelInvite');
  function showInviteForm() { inviteForm.style.display = 'block'; document.getElementById('invite_email').focus(); }
  inviteBtn && inviteBtn.addEventListener('click', showInviteForm);
  inviteBtnInCard && inviteBtnInCard.addEventListener('click', showInviteForm);
  cancelInvite && cancelInvite.addEventListener('click', function () { inviteForm.style.display = 'none'; });
</script>

<?php require template_path('partials/footer.php'); ?>
