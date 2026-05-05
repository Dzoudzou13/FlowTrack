<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Board | FlowTrack';
$styles    = ['css/app.css', 'css/kanban.css'];
$scripts   = ['js/app.js', 'js/kanban.js'];

$project = $project ?? [];
$grouped = $grouped ?? ['backlog' => [], 'in_progress' => [], 'review' => [], 'done' => []];

$columns = [
    'backlog'     => 'Backlog',
    'in_progress' => 'In Progress',
    'review'      => 'Review',
    'done'        => 'Done',
];

require template_path('partials/header.php');

?>
<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <!-- Board header -->
      <div class="board-page-header">
        <div>
          <h1 class="page-title">Board</h1>
          <p class="page-subtitle">
            <a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);">Projekty</a> /
            <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id']), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);"><?= htmlspecialchars($project['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a> /
            Board
          </p>
        </div>
        <div class="board-filter-row">
          <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/tasks/create'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nový task
          </a>
        </div>
      </div>

      <!-- Kanban board -->
      <div class="board-wrap">
        <div class="board-layout">

          <?php foreach ($columns as $status => $label): ?>
            <div class="kanban-col" data-status="<?= $status ?>">
              <div class="kanban-col-header">
                <div class="kanban-col-dot"></div>
                <span class="kanban-col-title"><?= $label ?></span>
                <span class="kanban-col-count"><?= count($grouped[$status]) ?></span>
              </div>
              <div class="kanban-col-body">

                <?php foreach ($grouped[$status] as $t): ?>
                  <?php
                    $initials = strtoupper(substr($t['assignee_name'] ?? '', 0, 1) . (strstr($t['assignee_name'] ?? '', ' ') ? substr(strstr($t['assignee_name'], ' '), 1, 1) : ''));
                    $isOverdue = !empty($t['deadline']) && $t['status'] !== 'done' && strtotime($t['deadline']) < time();
                  ?>
                  <a href="<?= htmlspecialchars(app_url('/tasks/' . $t['id']), ENT_QUOTES, 'UTF-8') ?>" class="kanban-card" data-task-id="<?= $t['id'] ?>" style="text-decoration:none;display:block;">
                    <div class="kanban-card-priority">
                      <span class="priority-badge <?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span>
                    </div>
                    <div class="kanban-card-title"><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="kanban-card-footer">
                      <span class="kanban-card-project"><?= htmlspecialchars($project['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                      <div class="kanban-card-meta">
                        <?php if (!empty($t['deadline'])): ?>
                          <span class="kanban-card-deadline <?= $isOverdue ? 'overdue' : '' ?>"><?= date('j.n.', strtotime($t['deadline'])) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($t['assignee_name'])): ?>
                          <div class="kanban-card-avatar"><?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </a>
                <?php endforeach; ?>

                <button class="kanban-add-btn" onclick="window.location='<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/tasks/create'), ENT_QUOTES, 'UTF-8') ?>'">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 4.5v15m7.5-7.5h-15"/>
                  </svg>
                  Pridať task
                </button>
              </div>
            </div>
          <?php endforeach; ?>

        </div><!-- /.board-layout -->
      </div><!-- /.board-wrap -->

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<?php require template_path('partials/footer.php'); ?>
