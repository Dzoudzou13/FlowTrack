<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? (($task['title'] ?? 'Task') . ' | FlowTrack');
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

$statusLabels = [
    'backlog'     => 'Backlog',
    'in_progress' => 'In Progress',
    'review'      => 'Review',
    'done'        => 'Done',
];
$priorityLabels = [
    'low'      => 'Low',
    'medium'   => 'Medium',
    'high'     => 'High',
    'critical' => 'Critical',
];
$totalMinutes = array_sum(array_map(static fn(array $entry): int => (int) $entry['duration_minutes'], $entries ?? []));

require template_path('partials/header.php');

?>
<div class="app-layout">
  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">
    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">
      <div class="page-header">
        <p class="page-subtitle">
          <a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">Projects</a> /
          <a href="<?= htmlspecialchars(app_url('/projects/' . $task['project_id']), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);"><?= htmlspecialchars($task['project_name'] ?? 'Projekt', ENT_QUOTES, 'UTF-8') ?></a> /
          <a href="<?= htmlspecialchars(app_url('/projects/' . $task['project_id'] . '/board'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">Board</a> /
          <?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="task-detail-layout">
        <div class="task-detail-main">
          <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:20px;">
            <div class="task-status-dot <?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?>" style="margin-top:6px;width:9px;height:9px;"></div>
            <div style="flex:1;">
              <h1 class="page-title" style="font-size:22px;margin-bottom:8px;"><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></h1>
              <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <span class="priority-badge <?= htmlspecialchars($task['priority'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($priorityLabels[$task['priority']] ?? $task['priority'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="status-badge <?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($statusLabels[$task['status']] ?? $task['status'], ENT_QUOTES, 'UTF-8') ?></span>
              </div>
            </div>
            <a href="<?= htmlspecialchars(app_url('/tasks/' . $task['id'] . '/edit'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm">Upraviť</a>
          </div>

          <div class="section-block" style="margin-bottom:20px;">
            <div class="section-block-header">
              <span class="section-block-title">Popis</span>
            </div>
            <div class="section-block-body" style="padding:16px 20px;">
              <?php if (!empty($task['description'])): ?>
                <p style="font-size:14px;color:var(--text-secondary);line-height:1.7;white-space:pre-wrap;"><?= htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8') ?></p>
              <?php else: ?>
                <p style="font-size:14px;color:var(--text-muted);">Task zatiaľ nemá popis.</p>
              <?php endif; ?>
            </div>
          </div>

          <div class="section-block" style="margin-bottom:20px;">
            <div class="section-block-header">
              <span class="section-block-title">Time entries</span>
              <button class="btn btn-ghost btn-sm" id="logTimeBtn" type="button">Zaznamenať čas</button>
            </div>

            <div id="logTimeForm" style="display:none;padding:16px 20px;border-bottom:1px solid var(--border);background:var(--surface-raised);">
              <form method="POST" action="<?= htmlspecialchars(app_url('/tasks/' . $task['id'] . '/time'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-row" style="margin-bottom:14px;">
                  <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="started_at">Začiatok</label>
                    <input type="datetime-local" id="started_at" name="started_at" class="form-control" />
                  </div>
                  <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="duration_minutes">Trvanie (minúty)</label>
                    <input type="number" id="duration_minutes" name="duration_minutes" class="form-control" placeholder="napr. 90" min="1" required />
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                  <label class="form-label" for="te_description">Popis</label>
                  <input type="text" id="te_description" name="description" class="form-control" placeholder="Čo si robil?" />
                </div>
                <div style="display:flex;align-items:center;gap:12px;">
                  <label style="display:flex;align-items:center;gap:7px;font-size:13px;color:var(--text-secondary);cursor:pointer;">
                    <input type="checkbox" name="billable" value="1" checked style="accent-color:var(--accent);" />
                    Billable
                  </label>
                  <button type="submit" class="btn btn-primary btn-sm">Uložiť</button>
                  <button type="button" class="btn btn-ghost btn-sm" id="cancelLogTime">Zrušiť</button>
                </div>
              </form>
            </div>

            <div class="section-block-body" style="padding:6px 18px 12px;">
              <div class="time-entry-list">
                <?php if (!empty($entries)): ?>
                  <?php foreach ($entries as $entry): ?>
                    <div class="time-entry-item">
                      <span class="time-entry-duration"><?= intdiv((int) $entry['duration_minutes'], 60) ?>h <?= (int) $entry['duration_minutes'] % 60 ?>m</span>
                      <span class="time-entry-desc"><?= htmlspecialchars($entry['description'] ?: 'Bez popisu', ENT_QUOTES, 'UTF-8') ?></span>
                      <span class="time-entry-date"><?= htmlspecialchars(date('d.m.Y', strtotime($entry['started_at'])), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p style="font-size:14px;color:var(--text-muted);padding:12px 2px;">Zatiaľ tu nie je zaznamenaný čas.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="section-block">
            <div class="section-block-header">
              <span class="section-block-title">Komentáre</span>
              <span class="section-block-count"><?= count($comments ?? []) ?></span>
            </div>
            <div class="section-block-body" style="padding:16px 20px 8px;">
              <div class="comment-list">
                <?php foreach (($comments ?? []) as $comment): ?>
                  <div class="comment-item">
                    <div class="comment-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($comment['user_name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="comment-body">
                      <div class="comment-header">
                        <span class="comment-author"><?= htmlspecialchars($comment['user_name'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="comment-time"><?= htmlspecialchars(date('d.m.Y H:i', strtotime($comment['created_at'])), ENT_QUOTES, 'UTF-8') ?></span>
                      </div>
                      <div class="comment-text"><?= nl2br(htmlspecialchars($comment['body'], ENT_QUOTES, 'UTF-8')) ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <form method="POST" action="<?= htmlspecialchars(app_url('/tasks/' . $task['id'] . '/comments'), ENT_QUOTES, 'UTF-8') ?>" style="margin-top:14px;">
                <div class="form-group" style="margin-bottom:10px;">
                  <textarea name="body" class="form-control" rows="2" placeholder="Napíš komentár..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Pridať komentár</button>
              </form>
            </div>
          </div>
        </div>

        <div class="task-detail-sidebar">
          <div class="task-meta-row">
            <span class="task-meta-key">Status</span>
            <span class="status-badge <?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($statusLabels[$task['status']] ?? $task['status'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Priorita</span>
            <span class="priority-badge <?= htmlspecialchars($task['priority'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($priorityLabels[$task['priority']] ?? $task['priority'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Assignee</span>
            <span class="task-meta-value"><?= htmlspecialchars($task['assignee_name'] ?? 'Nepriradené', ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Projekt</span>
            <a href="<?= htmlspecialchars(app_url('/projects/' . $task['project_id']), ENT_QUOTES, 'UTF-8') ?>" class="task-meta-value" style="color:var(--accent);"><?= htmlspecialchars($task['project_name'], ENT_QUOTES, 'UTF-8') ?></a>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Deadline</span>
            <span class="task-meta-value"><?= $task['deadline'] ? htmlspecialchars(date('d.m.Y', strtotime($task['deadline'])), ENT_QUOTES, 'UTF-8') : 'Bez deadlinu' ?></span>
          </div>
          <div class="task-meta-row">
            <span class="task-meta-key">Vytvorené</span>
            <span class="task-meta-value"><?= htmlspecialchars(date('d.m.Y', strtotime($task['created_at'])), ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="task-meta-row" style="border-bottom:none;">
            <span class="task-meta-key">Odpracované</span>
            <span class="task-meta-value" style="font-weight:700;color:var(--text-primary);"><?= intdiv($totalMinutes, 60) ?>h <?= $totalMinutes % 60 ?>m</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var logTimeBtn = document.getElementById('logTimeBtn');
  var logTimeForm = document.getElementById('logTimeForm');
  var cancelBtn = document.getElementById('cancelLogTime');

  if (logTimeBtn && logTimeForm) {
    logTimeBtn.addEventListener('click', function () {
      logTimeForm.style.display = logTimeForm.style.display === 'none' ? 'block' : 'none';
    });
  }

  if (cancelBtn && logTimeForm) {
    cancelBtn.addEventListener('click', function () {
      logTimeForm.style.display = 'none';
    });
  }
</script>

<?php require template_path('partials/footer.php'); ?>
