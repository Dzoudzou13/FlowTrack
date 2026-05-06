<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Projekt | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

$project = $project ?? [];
$tasks   = $tasks   ?? [];
$entries = $entries ?? [];

$totalH   = intdiv((int) ($project['total_minutes'] ?? 0), 60);
$totalM   = (int) ($project['total_minutes'] ?? 0) % 60;
$billH    = intdiv((int) ($project['billable_minutes'] ?? 0), 60);
$billM    = (int) ($project['billable_minutes'] ?? 0) % 60;
$revenue  = ($project['billable_minutes'] ?? 0) / 60 * ($project['hourly_rate'] ?? 0);
$taskCnt  = (int) ($project['task_count'] ?? 0);
$doneCnt  = (int) ($project['done_count'] ?? 0);
$progress = $taskCnt > 0 ? round($doneCnt / $taskCnt * 100) : 0;

require template_path('partials/header.php');

?>
<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <!-- Project header -->
      <div class="project-detail-header">
        <div class="project-detail-dot" style="background: <?= htmlspecialchars($project['color'] ?? '#6366f1', ENT_QUOTES, 'UTF-8') ?>;"></div>
        <div class="project-detail-info">
          <h1 class="project-detail-name"><?= htmlspecialchars($project['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
          <?php if (!empty($project['description'])): ?>
            <p class="project-detail-desc"><?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8') ?></p>
          <?php endif; ?>
          <div class="project-detail-meta">
            <span class="status-badge <?= $project['status'] ?? 'active' ?>"><?= match($project['status'] ?? 'active') {
              'active'    => 'Active',
              'on_hold'   => 'On Hold',
              'completed' => 'Completed',
              'archived'  => 'Archived',
              default     => $project['status'] ?? '',
            } ?></span>
            <?php if (!empty($project['deadline'])): ?>
              <span class="project-detail-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                Deadline: <?= date('j.n.Y', strtotime($project['deadline'])) ?>
              </span>
            <?php endif; ?>
            <span class="project-detail-meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
              </svg>
              <?= number_format((float) ($project['hourly_rate'] ?? 0), 0) ?> €/h
            </span>
          </div>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
          <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/board'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost">Board</a>
          <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/edit'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost">Upraviť</a>
        </div>
      </div>

      <!-- Stats -->
      <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <div class="stat-card">
          <div class="stat-card-top"><span class="stat-card-label">Celkom taskov</span><div class="stat-card-icon indigo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div>
          <div class="stat-card-value"><?= $taskCnt ?></div>
          <div class="stat-card-sub"><?= $doneCnt ?> done · <?= $taskCnt - $doneCnt ?> aktívnych</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top"><span class="stat-card-label">Odpracované</span><div class="stat-card-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div>
          <div class="stat-card-value"><?= $totalH ?>h<?= $totalM > 0 ? ' ' . $totalM . 'm' : '' ?></div>
          <div class="stat-card-sub"><?= $billH ?>h billable</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top"><span class="stat-card-label">Revenue</span><div class="stat-card-icon amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div>
          <div class="stat-card-value"><?= number_format($revenue, 0, ',', ' ') ?> €</div>
          <div class="stat-card-sub"><?= $billH ?>h × <?= number_format((float)($project['hourly_rate']??0),0) ?> €/h</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top"><span class="stat-card-label">Postup</span><div class="stat-card-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg></div></div>
          <div class="stat-card-value"><?= $progress ?>%</div>
          <div class="stat-card-sub" style="margin-top:6px;"><div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:<?= $progress ?>%;"></div></div></div>
        </div>
      </div>

      <!-- Tasks & Time entries -->
      <div class="dashboard-grid">

        <div class="section-block">
          <div class="section-block-header">
            <span class="section-block-title">Tasky projektu</span>
            <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/board'), ENT_QUOTES, 'UTF-8') ?>" class="section-block-link">Otvoriť Board</a>
          </div>
          <div class="section-block-body">
            <?php if (empty($tasks)): ?>
              <div class="empty-state" style="padding: 24px 18px;">
                <div class="empty-state-title">Žiadne tasky zatiaľ</div>
                <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/board'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm" style="margin-top:8px;">Otvoriť Board</a>
              </div>
            <?php else: ?>
              <?php foreach (array_slice($tasks, 0, 6) as $t): ?>
                <?php
                  $sc = match($t['status']) {'in_progress'=>'in-progress','review'=>'review','done'=>'done',default=>''};
                  $overdue = !empty($t['deadline']) && $t['status'] !== 'done' && strtotime($t['deadline']) < time();
                ?>
                <a href="<?= htmlspecialchars(app_url('/tasks/' . $t['id']), ENT_QUOTES, 'UTF-8') ?>" class="task-list-item" style="text-decoration:none;">
                  <div class="task-status-dot <?= $sc ?>"></div>
                  <span class="task-list-name" <?= $t['status']==='done' ? 'style="opacity:.5;text-decoration:line-through;"' : '' ?>><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></span>
                  <div class="task-list-meta">
                    <span class="priority-badge <?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span>
                    <?php if (!empty($t['deadline'])): ?>
                      <span class="date-badge <?= $overdue ? 'overdue' : '' ?>"><?= date('j.n.', strtotime($t['deadline'])) ?></span>
                    <?php endif; ?>
                  </div>
                </a>
              <?php endforeach; ?>
              <?php if (count($tasks) > 6): ?>
                <div style="padding:10px 18px 6px;">
                  <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/board'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm">+<?= count($tasks)-6 ?> ďalších</a>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="section-block">
          <div class="section-block-header">
            <span class="section-block-title">Posledné záznamy</span>
            <a href="<?= htmlspecialchars(app_url('/time'), ENT_QUOTES, 'UTF-8') ?>" class="section-block-link">Všetky záznamy</a>
          </div>
          <div class="section-block-body" style="padding: 6px 18px 12px;">
            <?php if (empty($entries)): ?>
              <div class="empty-state" style="padding: 24px 0;">
                <div class="empty-state-title">Žiadne záznamy</div>
              </div>
            <?php else: ?>
              <div class="time-entry-list">
                <?php foreach (array_slice($entries, 0, 5) as $e): ?>
                  <?php $h = intdiv((int)$e['duration_minutes'],60); $m = (int)$e['duration_minutes']%60; ?>
                  <div class="time-entry-item">
                    <span class="time-entry-duration"><?= $h ?>h<?= $m > 0 ? ' '.$m.'m' : '' ?></span>
                    <span class="time-entry-desc"><?= htmlspecialchars($e['description'] ?? $e['task_title'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="time-entry-date"><?= date('j.n.', strtotime($e['started_at'])) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<?php require template_path('partials/footer.php'); ?>
