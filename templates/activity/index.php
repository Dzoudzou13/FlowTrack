<?php

declare(strict_types=1);

$pageTitle  = $pageTitle  ?? 'Activity Log | FlowTrack';
$styles     = ['css/app.css'];
$scripts    = ['js/app.js'];
$activities = $activities ?? [];
$users      = $users      ?? [];

// Zoskup aktivity podla datumu.
$grouped = [];
foreach ($activities as $act) {
    $day = date('Y-m-d', strtotime($act['created_at']));
    $grouped[$day][] = $act;
}

function activityLabel(array $act): string
{
    $meta = $act['meta'] ? json_decode($act['meta'], true) : [];
    return match($act['action']) {
        'created'        => 'vytvoril ' . ($act['entity_type'] === 'project' ? 'projekt' : 'task') . (($meta['name'] ?? $meta['title'] ?? '') !== '' ? ' <strong>' . htmlspecialchars($meta['name'] ?? $meta['title'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong>' : ''),
        'updated'        => 'upravil ' . ($act['entity_type'] === 'project' ? 'projekt' : 'task') . (isset($meta['name']) ? ' <strong>' . htmlspecialchars($meta['name'], ENT_QUOTES, 'UTF-8') . '</strong>' : ''),
        'status_changed' => 'presunul task z <strong>' . htmlspecialchars($meta['from'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong> → <strong>' . htmlspecialchars($meta['to'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong>',
        'logged'         => 'zalogoval <strong>' . round(($meta['minutes'] ?? 0) / 60, 1) . 'h</strong>',
        'comment_added'  => 'pridal komentár k tasku <strong>' . htmlspecialchars($meta['title'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong>',
        'member_invited' => 'pozval člena <strong>' . htmlspecialchars($meta['email'] ?? '', ENT_QUOTES, 'UTF-8') . '</strong>',
        'deleted'        => 'vymazal ' . htmlspecialchars($act['entity_type'], ENT_QUOTES, 'UTF-8'),
        default          => htmlspecialchars($act['action'], ENT_QUOTES, 'UTF-8'),
    };
}

function activityDotClass(string $action): string
{
    return match($action) {
        'created', 'member_invited' => 'accent',
        'status_changed'            => 'success',
        'logged'                    => 'warning',
        'deleted'                   => 'danger',
        default                     => '',
    };
}

function dayLabel(string $day): string
{
    $ts    = strtotime($day);
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $fmt = (new \IntlDateFormatter('sk_SK', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE))->format($ts);
    if ($day === $today) return 'Dnes — ' . $fmt;
    if ($day === $yesterday) return 'Včera — ' . $fmt;
    return date('j. n. Y', $ts);
}

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
            <h1 class="page-title">Activity Log</h1>
            <p class="page-subtitle">História všetkých akcií v workspace</p>
          </div>
        </div>
      </div>

      <!-- Filter bar -->
      <form method="GET" action="<?= htmlspecialchars(app_url('/activity'), ENT_QUOTES, 'UTF-8') ?>" class="filter-bar">
        <span class="filter-bar-label">Filter:</span>
        <select name="type" class="filter-select" onchange="this.form.submit()">
          <option value="">Všetky typy</option>
          <option value="project" <?= ($_GET['type'] ?? '') === 'project' ? 'selected' : '' ?>>Projekty</option>
          <option value="task" <?= ($_GET['type'] ?? '') === 'task' ? 'selected' : '' ?>>Tasky</option>
          <option value="time_entry" <?= ($_GET['type'] ?? '') === 'time_entry' ? 'selected' : '' ?>>Čas</option>
          <option value="team" <?= ($_GET['type'] ?? '') === 'team' ? 'selected' : '' ?>>Team</option>
        </select>
        <select name="user_id" class="filter-select" onchange="this.form.submit()">
          <option value="">Všetci užívatelia</option>
          <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($_GET['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </form>

      <!-- Timeline -->
      <div class="section-block">
        <div class="timeline">

          <?php if (empty($grouped)): ?>
            <div class="empty-state" style="padding: 40px 0;">
              <div class="empty-state-title">Žiadna aktivita zatiaľ</div>
              <div class="empty-state-sub">Aktivita sa zaznamená keď vytvoríš projekt, task alebo zalogujeneš čas.</div>
            </div>
          <?php else: ?>
            <?php foreach ($grouped as $day => $acts): ?>
              <div class="timeline-date-group">
                <div class="timeline-date-label"><?= htmlspecialchars(dayLabel($day), ENT_QUOTES, 'UTF-8') ?></div>

                <?php foreach ($acts as $act): ?>
                  <div class="timeline-item" data-search-text="<?= htmlspecialchars(mb_strtolower($act['user_name'] . ' ' . $act['action'] . ' ' . $act['entity_type']), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="timeline-icon <?= activityDotClass($act['action']) ?>">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <?php if ($act['action'] === 'created'): ?>
                          <path d="M12 4.5v15m7.5-7.5h-15"/>
                        <?php elseif ($act['action'] === 'status_changed'): ?>
                          <path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/>
                          <path d="M3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25z"/>
                        <?php elseif ($act['action'] === 'logged'): ?>
                          <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <?php elseif ($act['action'] === 'comment_added'): ?>
                          <path d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                        <?php else: ?>
                          <path d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
                        <?php endif; ?>
                      </svg>
                    </div>
                    <div class="timeline-body">
                      <div class="timeline-text"><strong><?= htmlspecialchars($act['user_name'], ENT_QUOTES, 'UTF-8') ?></strong> <?= activityLabel($act) ?></div>
                      <div class="timeline-meta">
                        <span class="timeline-time"><?= date('H:i', strtotime($act['created_at'])) ?></span>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>

              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div><!-- /.timeline -->
      </div>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<?php require template_path('partials/footer.php'); ?>
