<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Implementovať auth modul | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

require template_path('partials/header.php');

?>
<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <!-- Breadcrumb -->
      <div class="page-header">
        <p class="page-subtitle">
          <a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">Projects</a> /
          <a href="<?= htmlspecialchars(app_url('/projects/1'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">FlowTrack MVP</a> /
          <a href="<?= htmlspecialchars(app_url('/projects/1/board'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">Board</a> /
          Implementovať auth modul
        </p>
      </div>

      <!-- Two-panel layout -->
      <div class="task-detail-layout">

        <!-- Main content -->
        <div class="task-detail-main">

          <!-- Task title & status -->
          <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 20px;">
            <div class="task-status-dot in-progress" style="margin-top: 6px; width: 9px; height: 9px;"></div>
            <div style="flex: 1;">
              <h1 class="page-title" style="font-size: 22px; margin-bottom: 8px;">Implementovať auth modul</h1>
              <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span class="priority-badge critical">Critical</span>
                <span class="status-badge in-progress">In Progress</span>
              </div>
            </div>
            <a href="<?= htmlspecialchars(app_url('/tasks/2/edit'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm">Upraviť</a>
          </div>

          <!-- Description -->
          <div class="section-block" style="margin-bottom: 20px;">
            <div class="section-block-header">
              <span class="section-block-title">Popis</span>
            </div>
            <div class="section-block-body" style="padding: 16px 20px;">
              <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.7;">
                Implementovať kompletný autentifikačný modul pre aplikáciu FlowTrack.
                Modul musí obsahovať: registráciu nového používateľa, prihlásenie cez email a heslo,
                odhlásenie, session guard (ochrana privátnych rout), hashovanie hesiel cez
                <code style="background: var(--surface-raised); padding: 1px 5px; border-radius: 4px; font-size: 12.5px; color: var(--accent);">password_hash()</code>
                a overenie cez
                <code style="background: var(--surface-raised); padding: 1px 5px; border-radius: 4px; font-size: 12.5px; color: var(--accent);">password_verify()</code>.
              </p>
              <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.7; margin-top: 10px;">
                Pri registrácii sa automaticky vytvorí workspace pre nového používateľa s rolou admin.
                Neprihlásený používateľ musí byť presmerovaný na /login pri pokuse o prístup na privátne routy.
              </p>
            </div>
          </div>

          <!-- Time entries -->
          <div class="section-block" style="margin-bottom: 20px;">
            <div class="section-block-header">
              <span class="section-block-title">Time entries</span>
              <button class="btn btn-ghost btn-sm" id="logTimeBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Zaznamenať čas
              </button>
            </div>

            <!-- Log time form (skrytý) -->
            <div id="logTimeForm" style="display: none; padding: 16px 20px; border-bottom: 1px solid var(--border); background: var(--surface-raised);">
              <form method="POST" action="<?= htmlspecialchars(app_url('/time'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="task_id" value="2" />
                <div class="form-row" style="margin-bottom: 14px;">
                  <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="started_at">Začiatok</label>
                    <input type="datetime-local" id="started_at" name="started_at" class="form-control" />
                  </div>
                  <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="duration">Trvanie (minúty)</label>
                    <input type="number" id="duration" name="duration_minutes" class="form-control" placeholder="napr. 90" min="1" />
                  </div>
                </div>
                <div class="form-group" style="margin-bottom: 14px;">
                  <label class="form-label" for="te_description">Popis</label>
                  <input type="text" id="te_description" name="description" class="form-control" placeholder="Čo si robil?" />
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                  <label style="display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--text-secondary); cursor: pointer;">
                    <input type="checkbox" name="billable" value="1" checked style="accent-color: var(--accent);" />
                    Billable
                  </label>
                  <button type="submit" class="btn btn-primary btn-sm">Uložiť</button>
                  <button type="button" class="btn btn-ghost btn-sm" id="cancelLogTime">Zrušiť</button>
                </div>
              </form>
            </div>

            <div class="section-block-body" style="padding: 6px 18px 12px;">
              <div class="time-entry-list">
                <div class="time-entry-item">
                  <span class="time-entry-duration">2h 30m</span>
                  <span class="time-entry-desc">Implementácia AuthController a login metódy</span>
                  <span class="time-entry-date">dnes</span>
                </div>
                <div class="time-entry-item">
                  <span class="time-entry-duration">1h 00m</span>
                  <span class="time-entry-desc">Napojenie na databázu a testovanie</span>
                  <span class="time-entry-date">včera</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Comments -->
          <div class="section-block">
            <div class="section-block-header">
              <span class="section-block-title">Komentáre</span>
              <span class="section-block-count">1</span>
            </div>
            <div class="section-block-body" style="padding: 16px 20px 8px;">

              <div class="comment-list">
                <div class="comment-item">
                  <div class="comment-avatar">JK</div>
                  <div class="comment-body">
                    <div class="comment-header">
                      <span class="comment-author">Jozef Kušnierik</span>
                      <span class="comment-time">dnes, 10:24</span>
                    </div>
                    <div class="comment-text">
                      Login funguje, ale treba ešte pridať guest_guard() aby sa prihlásený user
                      nepresmeroval na /login stránku znova. Tiež logout ešte nie je hotový.
                    </div>
                  </div>
                </div>
              </div>

              <!-- Nový komentár -->
              <form method="POST" action="<?= htmlspecialchars(app_url('/tasks/2/comments'), ENT_QUOTES, 'UTF-8') ?>" style="margin-top: 14px;">
                <div class="form-group" style="margin-bottom: 10px;">
                  <textarea name="content" class="form-control" rows="2" placeholder="Napíš komentár..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Pridať komentár</button>
              </form>

            </div>
          </div>

        </div><!-- /.task-detail-main -->

        <!-- Sidebar metadata -->
        <div class="task-detail-sidebar">
          <div class="task-meta-row">
            <span class="task-meta-key">Status</span>
            <span class="status-badge in-progress">In Progress</span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Priorita</span>
            <span class="priority-badge critical">Critical</span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Assignee</span>
            <span class="task-meta-value" style="display: flex; align-items: center; gap: 7px;">
              <div class="kanban-card-avatar">JK</div>
              Jozef K.
            </span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Projekt</span>
            <a href="<?= htmlspecialchars(app_url('/projects/1'), ENT_QUOTES, 'UTF-8') ?>" class="task-meta-value" style="color: var(--accent);">FlowTrack MVP</a>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Deadline</span>
            <span class="task-meta-value">5.5.2026</span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Vytvoril</span>
            <span class="task-meta-value">Jozef K.</span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Vytvorené</span>
            <span class="task-meta-value">2.5.2026</span>
          </div>
          <div class="task-meta-row" style="border-bottom: none;">
            <span class="task-meta-key">Odpracované</span>
            <span class="task-meta-value" style="font-weight: 700; color: var(--text-primary);">3h 30m</span>
          </div>
        </div>

      </div><!-- /.task-detail-layout -->

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<script>
  // Toggle log time form.
  var logTimeBtn = document.getElementById('logTimeBtn');
  var logTimeForm = document.getElementById('logTimeForm');
  var cancelBtn = document.getElementById('cancelLogTime');

  if (logTimeBtn) {
    logTimeBtn.addEventListener('click', function () {
      logTimeForm.style.display = logTimeForm.style.display === 'none' ? 'block' : 'none';
    });
  }

  if (cancelBtn) {
    cancelBtn.addEventListener('click', function () {
      logTimeForm.style.display = 'none';
    });
  }
</script>

<?php require template_path('partials/footer.php'); ?>
