<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Nový task | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];
$old       = $old ?? [];

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
            <h1 class="page-title">Nový task</h1>
            <p class="page-subtitle">
              <a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);">Projekty</a> /
              <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id']), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);"><?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?></a> /
              Nový task
            </p>
          </div>
        </div>
      </div>

      <div class="form-card">
        <?php if (!empty($formError)): ?>
          <div class="alert alert-error" style="margin-bottom:16px;"><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/tasks'), ENT_QUOTES, 'UTF-8') ?>">
          <div class="form-group">
            <label class="form-label" for="title">Názov tasku <span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="napr. Pripraviť dashboard" required />
          </div>

          <div class="form-group">
            <label class="form-label" for="description">Popis</label>
            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Čo treba spraviť?"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="status">Status</label>
              <select id="status" name="status" class="form-control">
                <?php foreach (['backlog' => 'Backlog', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $value => $label): ?>
                  <option value="<?= $value ?>" <?= (($old['status'] ?? 'backlog') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="priority">Priorita</label>
              <select id="priority" name="priority" class="form-control">
                <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'] as $value => $label): ?>
                  <option value="<?= $value ?>" <?= (($old['priority'] ?? 'medium') === $value) ? 'selected' : '' ?>><?= $label ?></option>
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
                  <option value="<?= (int) $member['id'] ?>" <?= ((string) ($old['assigned_to'] ?? '') === (string) $member['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="deadline">Deadline</label>
              <input type="date" id="deadline" name="deadline" class="form-control" value="<?= htmlspecialchars($old['deadline'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Vytvoriť task</button>
            <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/board'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost">Zrušiť</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require template_path('partials/footer.php'); ?>
