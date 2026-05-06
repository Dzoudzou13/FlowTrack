<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Board | FlowTrack';
$styles    = ['css/app.css', 'css/kanban.css'];
$scripts   = ['js/app.js', 'js/kanban.js'];

$grouped  = $grouped  ?? ['backlog' => [], 'in_progress' => [], 'review' => [], 'done' => []];
$projects = $projects ?? [];

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
          <p class="page-subtitle">Všetky tasky naprieč projektmi</p>
        </div>
        <div class="board-filter-row">
          <form method="GET" action="<?= htmlspecialchars(app_url('/board'), ENT_QUOTES, 'UTF-8') ?>" style="display:contents;">
            <select name="project_id" class="filter-select" onchange="this.form.submit()">
              <option value="">Všetky projekty</option>
              <?php foreach ($projects as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($_GET['project_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
            <select name="priority" class="filter-select" onchange="this.form.submit()">
              <option value="">Všetky priority</option>
              <?php foreach (['low'=>'Low','medium'=>'Medium','high'=>'High','critical'=>'Critical'] as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= ($_GET['priority'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </form>
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
                    $initials  = strtoupper(substr($t['assignee_name'] ?? '', 0, 1) . (strstr($t['assignee_name'] ?? '', ' ') ? substr(strstr($t['assignee_name'], ' '), 1, 1) : ''));
                    $isOverdue = !empty($t['deadline']) && $t['status'] !== 'done' && strtotime($t['deadline']) < time();
                  ?>
                  <a href="<?= htmlspecialchars(app_url('/tasks/' . $t['id']), ENT_QUOTES, 'UTF-8') ?>" class="kanban-card" data-task-id="<?= $t['id'] ?>" data-search-text="<?= htmlspecialchars(mb_strtolower($t['title'] . ' ' . ($t['project_name'] ?? '') . ' ' . ($t['assignee_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" style="text-decoration:none;display:block;">
                    <div class="kanban-card-priority">
                      <span class="priority-badge <?= $t['priority'] ?>"><?= ucfirst($t['priority']) ?></span>
                    </div>
                    <div class="kanban-card-title"><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="kanban-card-footer">
                      <span class="kanban-card-project" style="color:<?= htmlspecialchars($t['project_color'] ?? '#6366f1', ENT_QUOTES, 'UTF-8') ?>;"><?= htmlspecialchars($t['project_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
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

              </div>
            </div>
          <?php endforeach; ?>

        </div><!-- /.board-layout -->
      </div><!-- /.board-wrap -->

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<?php require template_path('partials/footer.php'); ?>
