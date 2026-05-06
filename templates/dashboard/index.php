<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Dashboard | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

$user           = auth_user();
$taskCounts     = $taskCounts     ?? ['backlog' => 0, 'in_progress' => 0, 'review' => 0, 'done' => 0];
$recentTasks    = $recentTasks    ?? [];
$totalsMonth    = $totalsMonth    ?? ['total_minutes' => 0, 'billable_minutes' => 0, 'revenue' => 0];
$activities     = $activities     ?? [];
$activeProjects = $activeProjects ?? 0;
$weekMinutes    = $weekMinutes    ?? 0;

$openTasks   = $taskCounts['in_progress'] + $taskCounts['review'] + $taskCounts['backlog'];
$weekH       = intdiv($weekMinutes, 60);
$weekM       = $weekMinutes % 60;

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
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Vitaj späť, <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>.</p>
          </div>
          <a href="<?= htmlspecialchars(app_url('/projects/create'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nový projekt
          </a>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="stat-grid">

        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Aktívne projekty</span>
            <div class="stat-card-icon indigo">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value"><?= $activeProjects ?></div>
          <div class="stat-card-sub"><?= $activeProjects === 0 ? 'Žiadne projekty zatiaľ' : 'aktívnych projektov' ?></div>
        </div>

        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Otvorené tasky</span>
            <div class="stat-card-icon blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value"><?= $openTasks ?></div>
          <div class="stat-card-sub"><?= $taskCounts['in_progress'] ?> in progress · <?= $taskCounts['review'] ?> review</div>
        </div>

        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Hodiny tento týždeň</span>
            <div class="stat-card-icon green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value"><?= $weekH ?>h<?= $weekM > 0 ? ' ' . $weekM . 'm' : '' ?></div>
          <div class="stat-card-sub"><?= $weekMinutes === 0 ? 'Začni trackovať čas' : 'zalogovaných tento týždeň' ?></div>
        </div>

        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Revenue tento mesiac</span>
            <div class="stat-card-icon amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value"><?= number_format((float) $totalsMonth['revenue'], 0, ',', ' ') ?> €</div>
          <div class="stat-card-sub"><?= round((float) $totalsMonth['billable_minutes'] / 60, 1) ?>h billable</div>
        </div>

      </div>

      <!-- Two column layout -->
      <div class="dashboard-grid">

        <!-- Left: Recent tasks -->
        <div>
          <div class="section-block">
            <div class="section-block-header">
              <span class="section-block-title">Tasky in progress</span>
              <span class="section-block-count"><?= count($recentTasks) ?></span>
            </div>
            <div class="section-block-body">

              <?php if (empty($recentTasks)): ?>
                <div class="empty-state" style="padding: 28px 18px;">
                  <div class="empty-state-title">Žiadne aktívne tasky</div>
                  <a href="<?= htmlspecialchars(app_url('/board'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm" style="margin-top:8px;">Otvoriť Board</a>
                </div>
              <?php else: ?>
                <?php foreach ($recentTasks as $t): ?>
                  <?php
                    $statusClass = match($t['status']) {
                      'in_progress' => 'in-progress',
                      'review'      => 'review',
                      'done'        => 'done',
                      default       => '',
                    };
                    $isOverdue = !empty($t['deadline']) && $t['status'] !== 'done' && strtotime($t['deadline']) < time();
                  ?>
                  <a href="<?= htmlspecialchars(app_url('/tasks/' . $t['id']), ENT_QUOTES, 'UTF-8') ?>" class="task-list-item" style="text-decoration:none;">
                    <div class="task-status-dot <?= $statusClass ?>"></div>
                    <span class="task-list-name"><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></span>
                    <div class="task-list-meta">
                      <span class="priority-badge <?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span>
                      <?php if (!empty($t['deadline'])): ?>
                        <span class="date-badge <?= $isOverdue ? 'overdue' : '' ?>"><?= date('j.n.Y', strtotime($t['deadline'])) ?></span>
                      <?php endif; ?>
                    </div>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>

              <div style="padding: 10px 18px 6px;">
                <a href="<?= htmlspecialchars(app_url('/board'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm">Otvoriť Board</a>
              </div>

            </div>
          </div>
        </div>

        <!-- Right: Activity feed -->
        <div>
          <div class="section-block">
            <div class="section-block-header">
              <span class="section-block-title">Posledná aktivita</span>
              <a href="<?= htmlspecialchars(app_url('/activity'), ENT_QUOTES, 'UTF-8') ?>" class="section-block-link">Zobraziť všetky</a>
            </div>
            <div class="activity-feed">

              <?php if (empty($activities)): ?>
                <div class="empty-state" style="padding: 28px 18px;">
                  <div class="empty-state-title">Žiadna aktivita zatiaľ</div>
                </div>
              <?php else: ?>
                <?php foreach ($activities as $act): ?>
                  <?php
                    $dotClass = match($act['action']) {
                      'created'        => 'accent',
                      'status_changed' => 'success',
                      'logged'         => 'warning',
                      'comment_added'  => '',
                      default          => '',
                    };
                    $meta   = $act['meta'] ? json_decode($act['meta'], true) : [];
                    $label  = match($act['action']) {
                      'created'        => 'vytvoril ' . ($act['entity_type'] === 'project' ? 'projekt' : 'task') . ($meta['name'] ?? $meta['title'] ?? '' ? ' <strong>' . htmlspecialchars($meta['name'] ?? $meta['title'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong>' : ''),
                      'updated'        => 'upravil ' . ($act['entity_type'] === 'project' ? 'projekt' : 'task') . (isset($meta['name']) ? ' <strong>' . htmlspecialchars($meta['name'], ENT_QUOTES, 'UTF-8') . '</strong>' : ''),
                      'status_changed' => 'presunul task z <strong>' . htmlspecialchars($meta['from'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong> → <strong>' . htmlspecialchars($meta['to'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong>',
                      'logged'         => 'zalogoval <strong>' . round(($meta['minutes'] ?? 0) / 60, 1) . 'h</strong>',
                      'comment_added'  => 'pridal komentár k tasku <strong>' . htmlspecialchars($meta['title'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong>',
                      default          => htmlspecialchars($act['action'], ENT_QUOTES, 'UTF-8'),
                    };
                    $ago = time() - strtotime($act['created_at']);
                    $timeStr = match(true) {
                      $ago < 3600   => 'pred ' . intdiv($ago, 60) . ' min',
                      $ago < 86400  => 'pred ' . intdiv($ago, 3600) . ' hod',
                      $ago < 172800 => 'včera',
                      default       => date('j.n.Y', strtotime($act['created_at'])),
                    };
                  ?>
                  <div class="activity-item">
                    <div class="activity-dot-wrap">
                      <div class="activity-dot <?= $dotClass ?>"></div>
                    </div>
                    <div class="activity-body">
                      <div class="activity-text"><strong><?= htmlspecialchars($act['user_name'], ENT_QUOTES, 'UTF-8') ?></strong> <?= $label ?></div>
                      <div class="activity-time"><?= $timeStr ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>

            </div>
          </div>
        </div>

      </div>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<?php require template_path('partials/footer.php'); ?>
