<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Board | FlowTrack';
$styles    = ['css/app.css', 'css/kanban.css'];
$scripts   = ['js/app.js', 'js/kanban.js'];

$grouped  = $grouped  ?? [];
$columns  = $columns  ?? [];
$projects = $projects ?? [];

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
          <button class="btn btn-primary btn-sm" onclick="openTaskDrawer('<?= htmlspecialchars($columns[0]['slug'] ?? 'backlog', ENT_QUOTES, 'UTF-8') ?>')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nový task
          </button>
        </div>
      </div>

      <!-- Kanban board -->
      <div class="board-wrap">
        <div class="board-layout">

          <?php foreach ($columns as $col): ?>
            <?php $slug = $col['slug']; $tasks = $grouped[$slug] ?? []; ?>
            <div class="kanban-col" data-status="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>" data-col-id="<?= (int) $col['id'] ?>">
              <div class="kanban-col-header">
                <div class="kanban-col-dot"></div>
                <span class="kanban-col-title"><?= htmlspecialchars($col['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="kanban-col-count"><?= count($tasks) ?></span>
                <form method="POST" action="<?= htmlspecialchars(app_url('/board/columns/' . $col['id'] . '/delete'), ENT_QUOTES, 'UTF-8') ?>" style="margin:0;" onsubmit="return confirm('Zmazať stĺpec „<?= htmlspecialchars($col['name'], ENT_QUOTES, 'UTF-8') ?>"? Tasky v ňom ostanú.')">
                  <button type="submit" class="kanban-col-del" title="Zmazať stĺpec">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </form>
              </div>
              <div class="kanban-col-body">

                <?php foreach ($tasks as $t): ?>
                  <?php
                    $initials  = strtoupper(substr($t['assignee_name'] ?? '', 0, 1) . (strstr($t['assignee_name'] ?? '', ' ') ? substr(strstr($t['assignee_name'], ' '), 1, 1) : ''));
                    $isOverdue = !empty($t['deadline']) && $t['status'] !== 'done' && strtotime($t['deadline']) < time();
                  ?>
                  <a href="<?= htmlspecialchars(app_url('/tasks/' . $t['id']), ENT_QUOTES, 'UTF-8') ?>" class="kanban-card" data-task-id="<?= $t['id'] ?>" style="text-decoration:none;display:block;">
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

                <button type="button" class="kanban-add-btn" onclick="openTaskDrawer('<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>')">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
                  Pridať task
                </button>
              </div>
            </div>
          <?php endforeach; ?>

          <!-- Add column -->
          <div class="kanban-col kanban-col-add" id="add-col-block">
            <button type="button" class="kanban-add-col-btn" id="add-col-toggle">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
              Pridať stĺpec
            </button>
            <form method="POST" action="<?= htmlspecialchars(app_url('/board/columns'), ENT_QUOTES, 'UTF-8') ?>" class="add-col-form" id="add-col-form" style="display:none;">
              <input type="text" name="name" placeholder="Názov stĺpca" class="form-control" maxlength="50" required>
              <div style="display:flex;gap:6px;margin-top:8px;">
                <button type="submit" class="btn btn-primary btn-sm" style="flex:1;">Pridať</button>
                <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('add-col-form').style.display='none';document.getElementById('add-col-toggle').style.display='flex';">Zrušiť</button>
              </div>
            </form>
          </div>

        </div><!-- /.board-layout -->
      </div><!-- /.board-wrap -->

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<!-- Task drawer overlay -->
<div class="task-drawer-overlay" id="task-drawer-overlay" onclick="closeTaskDrawer()"></div>

<!-- Task drawer -->
<div class="task-drawer" id="task-drawer">
  <div class="task-drawer-header">
    <span class="task-drawer-title">Nový task</span>
    <button type="button" class="task-drawer-close" onclick="closeTaskDrawer()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
  <form method="POST" action="<?= htmlspecialchars(app_url('/tasks/quick'), ENT_QUOTES, 'UTF-8') ?>" class="task-drawer-body">
    <input type="hidden" name="_back" value="<?= htmlspecialchars(app_url('/board'), ENT_QUOTES, 'UTF-8') ?>">

    <div class="form-group">
      <label class="form-label">Projekt <span style="color:var(--danger)">*</span></label>
      <select name="project_id" class="form-control" required>
        <option value="">— vyber projekt —</option>
        <?php foreach ($projects as $p): ?>
          <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Názov tasku <span style="color:var(--danger)">*</span></label>
      <input type="text" name="title" class="form-control" placeholder="Čo treba urobiť?" required>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div class="form-group">
        <label class="form-label">Priorita</label>
        <select name="priority" class="form-control">
          <option value="low">Low</option>
          <option value="medium" selected>Medium</option>
          <option value="high">High</option>
          <option value="critical">Critical</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Deadline</label>
        <input type="date" name="deadline" class="form-control">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Stĺpec</label>
      <select name="status" id="drawer-status-select" class="form-control">
        <?php foreach ($columns as $col): ?>
          <option value="<?= htmlspecialchars($col['slug'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($col['name'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:4px;">Vytvoriť task</button>
  </form>
</div>

<script>
  document.getElementById('add-col-toggle').addEventListener('click', function () {
    this.style.display = 'none';
    var form = document.getElementById('add-col-form');
    form.style.display = 'block';
    form.querySelector('input[name="name"]').focus();
  });

  function openTaskDrawer(status) {
    var sel = document.getElementById('drawer-status-select');
    if (sel) {
      for (var i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value === status) { sel.selectedIndex = i; break; }
      }
    }
    document.getElementById('task-drawer').classList.add('open');
    document.getElementById('task-drawer-overlay').classList.add('open');
    setTimeout(function () {
      var titleInput = document.querySelector('#task-drawer input[name="title"]');
      if (titleInput) titleInput.focus();
    }, 200);
  }

  function closeTaskDrawer() {
    document.getElementById('task-drawer').classList.remove('open');
    document.getElementById('task-drawer-overlay').classList.remove('open');
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeTaskDrawer();
  });
</script>

<?php require template_path('partials/footer.php'); ?>
