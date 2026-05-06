<?php

declare(strict_types=1);

$pageTitle   = $pageTitle   ?? 'Time Tracking | FlowTrack';
$styles      = ['css/app.css'];
$scripts     = ['js/app.js'];

$entries     = $entries     ?? [];
$projects    = $projects    ?? [];
$totals      = $totals      ?? ['total_minutes' => 0, 'billable_minutes' => 0, 'revenue' => 0];
$weekMinutes = $weekMinutes ?? 0;
$month       = $month       ?? date('Y-m');

$weekH  = intdiv($weekMinutes, 60);
$weekM  = $weekMinutes % 60;

$monthTotalH    = intdiv((int) $totals['total_minutes'], 60);
$monthTotalM    = (int) $totals['total_minutes'] % 60;
$monthBillH     = intdiv((int) $totals['billable_minutes'], 60);
$monthBillM     = (int) $totals['billable_minutes'] % 60;
$monthRevenue   = (float) $totals['revenue'];
$entryCount     = count($entries);

$weekBillable = 0;
$weekStart    = date('Y-m-d', strtotime('monday this week'));
$weekEnd      = date('Y-m-d', strtotime('sunday this week'));
foreach ($entries as $e) {
    $eDate = date('Y-m-d', strtotime($e['started_at']));
    if ($e['billable'] && $eDate >= $weekStart && $eDate <= $weekEnd) {
        $weekBillable += (int) $e['duration_minutes'];
    }
}
$weekBillH = intdiv($weekBillable, 60);
$weekBillM = $weekBillable % 60;

require template_path('partials/header.php');

?>
<style>
.entry-bar-wrap {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 10px 14px;
  margin-bottom: 24px;
}
.entry-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.entry-bar-sep {
  width: 1px;
  height: 28px;
  background: var(--border);
  flex-shrink: 0;
}
.entry-desc-input {
  flex: 1;
  min-width: 160px;
  background: transparent;
  border: none;
  outline: none;
  font-size: 14px;
  color: var(--text-primary);
  padding: 6px 2px;
}
.entry-desc-input::placeholder { color: var(--text-muted); }
.entry-proj-select {
  background: var(--surface-hover, var(--surface));
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 5px 8px;
  font-size: 13px;
  color: var(--text-primary);
  cursor: pointer;
  max-width: 160px;
}
.entry-proj-select:focus { outline: none; border-color: var(--accent); }
.billable-btn {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: 2px solid var(--border);
  background: transparent;
  color: var(--text-muted);
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  display: grid;
  place-items: center;
  transition: all 0.15s;
  flex-shrink: 0;
}
.billable-btn.active { border-color: var(--accent); color: var(--accent); }
.mode-toggle {
  display: flex;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  flex-shrink: 0;
}
.mode-btn {
  width: 32px;
  height: 30px;
  background: transparent;
  border: none;
  cursor: pointer;
  color: var(--text-muted);
  display: grid;
  place-items: center;
  transition: all 0.15s;
}
.mode-btn.active { background: var(--accent); color: #fff; }
.mode-btn svg { width: 14px; height: 14px; stroke-width: 2; }
.timer-display {
  font-size: 18px;
  font-weight: 700;
  color: var(--text-secondary);
  font-variant-numeric: tabular-nums;
  min-width: 80px;
  text-align: center;
  letter-spacing: 0.02em;
}
.timer-display.running { color: var(--accent); }
.dur-input {
  width: 68px;
  background: transparent;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 5px 8px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
  text-align: center;
}
.dur-input:focus { outline: none; border-color: var(--accent); }
.manual-date-input {
  background: transparent;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 5px 8px;
  font-size: 13px;
  color: var(--text-primary);
}
.manual-date-input:focus { outline: none; border-color: var(--accent); }
.entry-start-btn.running {
  background: #ef4444 !important;
  border-color: #ef4444 !important;
}
</style>

<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <!-- Page header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Time Tracking</h1>
          <p class="page-subtitle">Prehľad odpracovaného času</p>
        </div>
      </div>

      <!-- Toggl-style entry bar -->
      <div class="entry-bar-wrap">
        <div class="entry-bar">

          <!-- Description -->
          <input type="text" id="entryDesc" class="entry-desc-input" placeholder="Čo si robil?" />

          <div class="entry-bar-sep"></div>

          <!-- Project -->
          <select id="entryProject" class="entry-proj-select">
            <option value="">Projekt</option>
            <?php foreach ($projects as $p): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>

          <!-- Billable toggle -->
          <button type="button" id="entryBillable" class="billable-btn active" title="Billable">$</button>

          <div class="entry-bar-sep"></div>

          <!-- Mode toggle: Timer | Manual -->
          <div class="mode-toggle" title="Prepnúť režim">
            <button type="button" class="mode-btn active" id="modeTimerBtn" title="Časovač">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </button>
            <button type="button" class="mode-btn" id="modeManualBtn" title="Manuálne zadanie">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
              </svg>
            </button>
          </div>

          <!-- Timer section -->
          <div id="timerSection" style="display:flex;align-items:center;gap:8px;">
            <span class="timer-display" id="timerDisplay">00:00:00</span>
            <button type="button" class="btn btn-primary entry-start-btn" id="timerStartStop">Štart</button>
          </div>

          <!-- Manual section -->
          <div id="manualSection" style="display:none;align-items:center;gap:8px;">
            <input type="date" id="manualDate" class="manual-date-input" value="<?= date('Y-m-d') ?>" />
            <input type="text" id="manualDur" class="dur-input" placeholder="0:00" title="Trvanie: napr. 1:30 alebo 90" />
            <button type="button" class="btn btn-primary" id="manualAdd">Pridať</button>
          </div>

        </div>
      </div>

      <!-- Hidden form pre odoslanie záznamu -->
      <form id="entryForm" method="POST" action="<?= htmlspecialchars(app_url('/time'), ENT_QUOTES, 'UTF-8') ?>" style="display:none;">
        <input type="hidden" name="project_id"       id="fProjectId" />
        <input type="hidden" name="description"      id="fDescription" />
        <input type="hidden" name="started_at"       id="fStartedAt" />
        <input type="hidden" name="duration_minutes" id="fDurationMinutes" />
        <input type="hidden" name="billable"         id="fBillable" />
      </form>

      <!-- Summary cards -->
      <div class="stat-grid" style="margin-bottom:24px;">
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Tento týždeň</span>
            <div class="stat-card-icon indigo">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value"><?= $weekH ?>h <?= $weekM > 0 ? $weekM . 'm' : '00m' ?></div>
          <div class="stat-card-sub"><?= $weekBillH ?>h <?= $weekBillM > 0 ? $weekBillM . 'm' : '00m' ?> billable</div>
        </div>

        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Tento mesiac</span>
            <div class="stat-card-icon green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value"><?= $monthTotalH ?>h <?= $monthTotalM > 0 ? $monthTotalM . 'm' : '00m' ?></div>
          <div class="stat-card-sub"><?= $monthBillH ?>h <?= $monthBillM > 0 ? $monthBillM . 'm' : '00m' ?> billable</div>
        </div>

        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Billable hodnota</span>
            <div class="stat-card-icon amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value"><?= number_format($monthRevenue, 0, ',', ' ') ?> €</div>
          <div class="stat-card-sub">za <?= date('F Y', strtotime($month . '-01')) ?></div>
        </div>

        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Záznamy</span>
            <div class="stat-card-icon blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value"><?= $entryCount ?></div>
          <div class="stat-card-sub">za <?= date('F Y', strtotime($month . '-01')) ?></div>
        </div>
      </div>

      <!-- Filter bar -->
      <form method="GET" action="<?= htmlspecialchars(app_url('/time'), ENT_QUOTES, 'UTF-8') ?>" class="filter-bar">
        <span class="filter-bar-label">Filter:</span>
        <select name="project_id" class="filter-select" onchange="this.form.submit()">
          <option value="">Všetky projekty</option>
          <?php foreach ($projects as $p): ?>
            <option value="<?= $p['id'] ?>" <?= ($_GET['project_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <select name="billable" class="filter-select" onchange="this.form.submit()">
          <option value="">Billable aj nie</option>
          <option value="1" <?= ($_GET['billable'] ?? '') === '1' ? 'selected' : '' ?>>Len billable</option>
          <option value="0" <?= ($_GET['billable'] ?? '') === '0' ? 'selected' : '' ?>>Len non-billable</option>
        </select>
        <input type="month" name="month" class="filter-select"
          value="<?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>"
          onchange="this.form.submit()" />
      </form>

      <!-- Time entries table -->
      <div class="table-wrap">
        <?php if (empty($entries)): ?>
          <div class="empty-state" style="padding:40px 0;">
            <div class="empty-state-title">Žiadne záznamy</div>
            <div class="empty-state-sub">Použi lištu vyššie a pridaj prvý záznam.</div>
          </div>
        <?php else: ?>
          <table class="data-table">
            <thead>
              <tr>
                <th>Dátum</th>
                <th>Projekt</th>
                <th>Task</th>
                <th>Popis</th>
                <th>Trvanie</th>
                <th>Billable</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($entries as $e):
                $h   = intdiv((int) $e['duration_minutes'], 60);
                $min = (int) $e['duration_minutes'] % 60;

                $ts    = strtotime($e['started_at']);
                $today = date('Y-m-d');
                $yest  = date('Y-m-d', strtotime('-1 day'));
                $eDay  = date('Y-m-d', $ts);
                if ($eDay === $today)      $dateLabel = 'Dnes, '   . date('H:i', $ts);
                elseif ($eDay === $yest)   $dateLabel = 'Včera, '  . date('H:i', $ts);
                else                       $dateLabel = date('j.n.Y', $ts) . ', ' . date('H:i', $ts);
              ?>
              <tr data-search-text="<?= htmlspecialchars(mb_strtolower($e['project_name'] . ' ' . ($e['task_title'] ?? '') . ' ' . ($e['description'] ?? '')), ENT_QUOTES, 'UTF-8') ?>">
                <td class="muted"><?= htmlspecialchars($dateLabel, ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <a href="<?= htmlspecialchars(app_url('/projects/' . $e['project_id']), ENT_QUOTES, 'UTF-8') ?>"
                     style="color:<?= htmlspecialchars($e['project_color'] ?? '#6366f1', ENT_QUOTES, 'UTF-8') ?>;font-weight:600;">
                    <?= htmlspecialchars($e['project_name'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </td>
                <td>
                  <?php if (!empty($e['task_title'])): ?>
                    <a href="<?= htmlspecialchars(app_url('/tasks/' . $e['task_id']), ENT_QUOTES, 'UTF-8') ?>"
                       style="color:var(--text-primary);">
                      <?= htmlspecialchars($e['task_title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                  <?php else: ?>
                    <span class="muted">—</span>
                  <?php endif; ?>
                </td>
                <td style="max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  <?= htmlspecialchars($e['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td><strong><?= $h ?>h <?= $min > 0 ? $min . 'm' : '00m' ?></strong></td>
                <td>
                  <?php if ($e['billable']): ?>
                    <span class="billable-dot yes"></span>Áno
                  <?php else: ?>
                    <span class="billable-dot no"></span>Nie
                  <?php endif; ?>
                </td>
                <td class="right">
                  <form method="POST"
                        action="<?= htmlspecialchars(app_url('/time/' . $e['id'] . '/delete'), ENT_QUOTES, 'UTF-8') ?>"
                        onsubmit="return confirm('Zmazať záznam?');"
                        style="display:inline;">
                    <button type="submit" class="btn btn-ghost btn-sm">Zmazať</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<script>
(function () {
  var TIMER_KEY = 'ft-active-timer';

  var descInput    = document.getElementById('entryDesc');
  var projSelect   = document.getElementById('entryProject');
  var billableBtn  = document.getElementById('entryBillable');
  var modeTimerBtn = document.getElementById('modeTimerBtn');
  var modeManualBtn= document.getElementById('modeManualBtn');
  var timerSection = document.getElementById('timerSection');
  var manualSection= document.getElementById('manualSection');
  var timerDisplay = document.getElementById('timerDisplay');
  var timerStartStop = document.getElementById('timerStartStop');
  var manualDate   = document.getElementById('manualDate');
  var manualDur    = document.getElementById('manualDur');
  var manualAdd    = document.getElementById('manualAdd');
  var entryForm    = document.getElementById('entryForm');

  var billableActive = true;
  var timerInterval  = null;
  var isManualMode   = false;

  // --- Billable toggle ---
  billableBtn.addEventListener('click', function () {
    billableActive = !billableActive;
    billableBtn.classList.toggle('active', billableActive);
  });

  // --- Mode toggle ---
  modeTimerBtn.addEventListener('click', function () {
    if (isManualMode) switchMode('timer');
  });
  modeManualBtn.addEventListener('click', function () {
    if (!isManualMode) switchMode('manual');
  });

  function switchMode(mode) {
    isManualMode = (mode === 'manual');
    modeTimerBtn.classList.toggle('active', !isManualMode);
    modeManualBtn.classList.toggle('active', isManualMode);
    timerSection.style.display  = isManualMode ? 'none' : 'flex';
    manualSection.style.display = isManualMode ? 'flex' : 'none';
  }

  // --- Timer ---
  function getTimer() {
    try { return JSON.parse(localStorage.getItem(TIMER_KEY)); } catch(e) { return null; }
  }
  function saveTimer(data) {
    localStorage.setItem(TIMER_KEY, JSON.stringify(data));
  }
  function clearTimer() {
    localStorage.removeItem(TIMER_KEY);
  }

  function formatElapsed(seconds) {
    var h = Math.floor(seconds / 3600);
    var m = Math.floor((seconds % 3600) / 60);
    var s = seconds % 60;
    return pad(h) + ':' + pad(m) + ':' + pad(s);
  }
  function pad(n) { return n < 10 ? '0' + n : '' + n; }

  function tickTimer() {
    var t = getTimer();
    if (!t) { stopTicking(); return; }
    var elapsed = Math.floor((Date.now() - t.startedAt) / 1000);
    timerDisplay.textContent = formatElapsed(elapsed);
  }

  function startTicking() {
    tickTimer();
    timerInterval = setInterval(tickTimer, 1000);
  }

  function stopTicking() {
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
  }

  function setRunningUI(running) {
    timerDisplay.classList.toggle('running', running);
    timerStartStop.textContent = running ? 'Stop' : 'Štart';
    timerStartStop.classList.toggle('running', running);
  }

  // Restore timer state on page load
  var existing = getTimer();
  if (existing) {
    descInput.value = existing.description || '';
    if (existing.projectId) projSelect.value = existing.projectId;
    billableActive = !!existing.billable;
    billableBtn.classList.toggle('active', billableActive);
    setRunningUI(true);
    startTicking();
  }

  timerStartStop.addEventListener('click', function () {
    var t = getTimer();
    if (t) {
      // STOP
      var durationMs = Date.now() - t.startedAt;
      var durationMin = Math.max(1, Math.round(durationMs / 60000));
      var startedDate = new Date(t.startedAt);
      var startedAt = startedDate.getFullYear() + '-'
        + pad(startedDate.getMonth() + 1) + '-'
        + pad(startedDate.getDate()) + ' '
        + pad(startedDate.getHours()) + ':'
        + pad(startedDate.getMinutes()) + ':00';

      document.getElementById('fProjectId').value      = t.projectId || '';
      document.getElementById('fDescription').value    = t.description || '';
      document.getElementById('fStartedAt').value      = startedAt;
      document.getElementById('fDurationMinutes').value= durationMin;
      document.getElementById('fBillable').value       = t.billable ? '1' : '';

      clearTimer();
      stopTicking();
      setRunningUI(false);
      timerDisplay.textContent = '00:00:00';
      entryForm.submit();
    } else {
      // START
      if (!projSelect.value) {
        projSelect.focus();
        projSelect.style.borderColor = '#ef4444';
        setTimeout(function () { projSelect.style.borderColor = ''; }, 2000);
        return;
      }
      var data = {
        startedAt:   Date.now(),
        projectId:   projSelect.value,
        description: descInput.value.trim(),
        billable:    billableActive,
      };
      saveTimer(data);
      setRunningUI(true);
      startTicking();
    }
  });

  // --- Manual mode ---
  function parseDuration(str) {
    str = str.trim();
    var colonMatch = str.match(/^(\d+):(\d{1,2})$/);
    if (colonMatch) return parseInt(colonMatch[1]) * 60 + parseInt(colonMatch[2]);
    var numMatch = str.match(/^(\d+)$/);
    if (numMatch) return parseInt(numMatch[1]);
    var total = 0;
    var hMatch = str.match(/(\d+)h/i);
    var mMatch = str.match(/(\d+)m/i);
    if (hMatch) total += parseInt(hMatch[1]) * 60;
    if (mMatch) total += parseInt(mMatch[1]);
    return total;
  }

  manualAdd.addEventListener('click', function () {
    var durMin = parseDuration(manualDur.value);
    if (!projSelect.value) {
      projSelect.focus();
      projSelect.style.borderColor = '#ef4444';
      setTimeout(function () { projSelect.style.borderColor = ''; }, 2000);
      return;
    }
    if (durMin <= 0) {
      manualDur.focus();
      manualDur.style.borderColor = '#ef4444';
      setTimeout(function () { manualDur.style.borderColor = ''; }, 2000);
      return;
    }

    var date = manualDate.value || new Date().toISOString().slice(0, 10);
    var now = new Date();
    var startedAt = date + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':00';

    document.getElementById('fProjectId').value       = projSelect.value;
    document.getElementById('fDescription').value     = descInput.value.trim();
    document.getElementById('fStartedAt').value       = startedAt;
    document.getElementById('fDurationMinutes').value = durMin;
    document.getElementById('fBillable').value        = billableActive ? '1' : '';

    entryForm.submit();
  });

  // Allow Enter in duration field
  manualDur.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') manualAdd.click();
  });
})();
</script>

<?php require template_path('partials/footer.php'); ?>
