<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Upraviť task | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

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
            <h1 class="page-title">Upraviť task</h1>
            <p class="page-subtitle">
              <a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);">Projekty</a> /
              <a href="<?= htmlspecialchars(app_url('/projects/' . $task['project_id']), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);"><?= htmlspecialchars($task['project_name'] ?? 'Projekt', ENT_QUOTES, 'UTF-8') ?></a> /
              <a href="<?= htmlspecialchars(app_url('/tasks/' . $task['id']), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);"><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></a> /
              Upraviť
            </p>
          </div>
        </div>
      </div>

      <div class="form-card">
        <?php if (!empty($formError)): ?>
          <div class="alert alert-error" style="margin-bottom:16px;"><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars(app_url('/tasks/' . $task['id'] . '/update'), ENT_QUOTES, 'UTF-8') ?>">
          <div class="form-group">
            <label class="form-label" for="title">Názov tasku <span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($task['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
          </div>

          <div class="form-group">
            <label class="form-label" for="description">Popis</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($task['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="status">Status</label>
              <select id="status" name="status" class="form-control">
                <?php foreach (['backlog' => 'Backlog', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $value => $label): ?>
                  <option value="<?= $value ?>" <?= (($task['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="priority">Priorita</label>
              <select id="priority" name="priority" class="form-control">
                <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $value => $label): ?>
                  <option value="<?= $value ?>" <?= (($task['priority'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="assigned_to">Priradiť</label>
              <select id="assigned_to" name="assigned_to" class="form-control">
                <option value="">Nepriradené</option>
                <?php foreach (($members ?? []) as $member): ?>
                  <option value="<?= (int) $member['id'] ?>" <?= ((string) ($task['assigned_to'] ?? '') === (string) $member['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="deadline">Deadline</label>
              <input type="date" id="deadline" name="deadline" class="form-control" value="<?= htmlspecialchars($task['deadline'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Uložiť zmeny</button>
            <a href="<?= htmlspecialchars(app_url('/tasks/' . $task['id']), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost">Zrušiť</a>
          </div>
        </form>

        <form method="POST" action="<?= htmlspecialchars(app_url('/tasks/' . $task['id'] . '/delete'), ENT_QUOTES, 'UTF-8') ?>" style="margin-top:18px;padding-top:18px;border-top:1px solid var(--border);">
          <button type="submit" class="btn btn-ghost" onclick="return confirm('Naozaj chceš vymazať tento task?')">Vymazať task</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require template_path('partials/footer.php'); ?>
