<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Projekty | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];
$projects  = $projects ?? [];

require template_path('partials/header.php');

?>
<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <!-- Page header -->
      <div class="page-header">
        <div class="page-header-row">
          <div>
            <h1 class="page-title">Projekty</h1>
            <p class="page-subtitle"><?= count($projects) ?> projektov v workspace</p>
          </div>
          <a href="<?= htmlspecialchars(app_url('/projects/create'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nový projekt
          </a>
        </div>
      </div>

      <?php if (empty($projects)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
            </svg>
          </div>
          <div class="empty-state-title">Žiadne projekty zatiaľ</div>
          <div class="empty-state-sub">Vytvor svoj prvý projekt a začni trackovať čas a tasky.</div>
          <a href="<?= htmlspecialchars(app_url('/projects/create'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary">Vytvoriť projekt</a>
        </div>
      <?php else: ?>
        <div class="project-grid">
          <?php foreach ($projects as $p): ?>
            <?php
              $totalH  = intdiv((int) $p['total_minutes'], 60);
              $totalM  = (int) $p['total_minutes'] % 60;
              $timeStr = $totalH . 'h' . ($totalM > 0 ? ' ' . $totalM . 'm' : '');
            ?>
            <div class="project-card" data-search-text="<?= htmlspecialchars(mb_strtolower($p['name'] . ' ' . ($p['description'] ?? '')), ENT_QUOTES, 'UTF-8') ?>">
              <div class="project-card-stripe" style="background: <?= htmlspecialchars($p['color'], ENT_QUOTES, 'UTF-8') ?>;"></div>
              <div class="project-card-body">
                <div class="project-card-header">
                  <a href="<?= htmlspecialchars(app_url('/projects/' . $p['id']), ENT_QUOTES, 'UTF-8') ?>" class="project-card-name">
                    <?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                  <span class="status-badge <?= $p['status'] ?>"><?= match($p['status']) {
                    'active'    => 'Active',
                    'on_hold'   => 'On Hold',
                    'completed' => 'Completed',
                    'archived'  => 'Archived',
                    default     => $p['status'],
                  } ?></span>
                </div>
                <?php if (!empty($p['description'])): ?>
                  <p class="project-card-desc"><?= htmlspecialchars(mb_substr($p['description'], 0, 80), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($p['description']) > 80 ? '…' : '' ?></p>
                <?php endif; ?>
                <div class="project-card-meta">
                  <span><?= (int) $p['task_count'] ?> taskov</span>
                  <span><?= $timeStr ?></span>
                  <?php if (!empty($p['deadline'])): ?>
                    <span><?= date('j.n.Y', strtotime($p['deadline'])) ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="project-card-footer">
                <span style="font-size:12px; color:var(--text-muted);"><?= number_format((float) $p['hourly_rate'], 0) ?> €/h</span>
                <a href="<?= htmlspecialchars(app_url('/projects/' . $p['id'] . '/board'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm">Board</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<?php require template_path('partials/footer.php'); ?>
