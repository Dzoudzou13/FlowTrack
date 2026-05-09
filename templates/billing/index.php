<?php

declare(strict_types=1);

$pageTitle   = $pageTitle   ?? 'Billing | FlowTrack';
$styles      = ['css/app.css'];
$scripts     = ['js/app.js'];

$projects    = $projects    ?? [];
$totals      = $totals      ?? ['total_minutes' => 0, 'billable_minutes' => 0, 'revenue' => 0];
$type        = $type        ?? 'month';
$period      = $period      ?? date('Y-m');
$prevPeriod  = $prevPeriod  ?? date('Y-m', strtotime('-1 month'));
$nextPeriod  = $nextPeriod  ?? date('Y-m', strtotime('+1 month'));
$periodLabel = $periodLabel ?? date('F Y', strtotime($period . '-01'));

$totalBillH      = intdiv((int) $totals['billable_minutes'], 60);
$totalBillM      = (int) $totals['billable_minutes'] % 60;
$totalNonH       = intdiv(max(0, (int)$totals['total_minutes'] - (int)$totals['billable_minutes']), 60);
$totalNonM       = max(0, (int)$totals['total_minutes'] - (int)$totals['billable_minutes']) % 60;
$totalRevenue    = (float) $totals['revenue'];
$billablePercent = $totals['total_minutes'] > 0 ? round((int)$totals['billable_minutes'] / (int)$totals['total_minutes'] * 100) : 0;

$billingUrl = htmlspecialchars(app_url('/billing'), ENT_QUOTES, 'UTF-8');

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
            <h1 class="page-title">Billing</h1>
            <p class="page-subtitle">Prehľad billable hodín a odhadovaných tržieb</p>
          </div>
          <div class="period-controls">
            <div class="period-toggle">
              <a href="<?= $billingUrl ?>?type=week&period=<?= htmlspecialchars(date('Y-m-d', strtotime('monday this week')), ENT_QUOTES, 'UTF-8') ?>"
                 class="period-toggle-btn <?= $type === 'week'  ? 'active' : '' ?>">Týždeň</a>
              <a href="<?= $billingUrl ?>?type=month&period=<?= htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8') ?>"
                 class="period-toggle-btn <?= $type === 'month' ? 'active' : '' ?>">Mesiac</a>
              <a href="<?= $billingUrl ?>?type=year&period=<?= htmlspecialchars(date('Y'), ENT_QUOTES, 'UTF-8') ?>"
                 class="period-toggle-btn <?= $type === 'year'  ? 'active' : '' ?>">Rok</a>
            </div>
            <div class="period-nav">
              <a href="<?= $billingUrl ?>?type=<?= $type ?>&period=<?= htmlspecialchars($prevPeriod, ENT_QUOTES, 'UTF-8') ?>" class="period-nav-arrow" title="Predchádzajúce">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
              </a>
              <span class="period-nav-label"><?= htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8') ?></span>
              <a href="<?= $billingUrl ?>?type=<?= $type ?>&period=<?= htmlspecialchars($nextPeriod, ENT_QUOTES, 'UTF-8') ?>" class="period-nav-arrow" title="Nasledujúce">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary cards -->
      <div class="billing-grid">
        <div class="billing-card">
          <div class="billing-card-label">Celkové tržby</div>
          <div class="billing-card-value"><?= number_format($totalRevenue, 0, ',', ' ') ?> €</div>
          <div class="billing-card-sub">za <?= htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div class="billing-card">
          <div class="billing-card-label">Billable hodiny</div>
          <div class="billing-card-value"><?= $totalBillH ?>h <?= $totalBillM > 0 ? $totalBillM . 'm' : '00m' ?></div>
          <div class="billing-card-sub">z celkových <?= intdiv((int)$totals['total_minutes'], 60) ?>h</div>
        </div>
        <div class="billing-card">
          <div class="billing-card-label">Non-billable hodiny</div>
          <div class="billing-card-value"><?= $totalNonH ?>h <?= $totalNonM > 0 ? $totalNonM . 'm' : '00m' ?></div>
          <div class="billing-card-sub">interná práca</div>
        </div>
      </div>

      <!-- Per-project breakdown -->
      <div class="section-block" style="margin-bottom:24px;">
        <div class="section-block-header">
          <span class="section-block-title">Revenue podľa projektu</span>
        </div>
        <div class="table-wrap" style="border:none;border-radius:0;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Projekt</th>
                <th>Billable hodiny</th>
                <th>Non-billable</th>
                <th>Sadzba</th>
                <th>Revenue</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($projects as $p): ?>
                <?php
                  $bH = intdiv((int)$p['billable_minutes'], 60);
                  $bM = (int)$p['billable_minutes'] % 60;
                  $nH = intdiv((int)$p['non_billable_minutes'], 60);
                  $nM = (int)$p['non_billable_minutes'] % 60;
                ?>
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                      <div style="width:8px;height:8px;border-radius:50%;background:<?= htmlspecialchars($p['color'], ENT_QUOTES, 'UTF-8') ?>;flex-shrink:0;"></div>
                      <a href="<?= htmlspecialchars(app_url('/projects/' . $p['id']), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--text-primary);font-weight:600;"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                  </td>
                  <td><strong><?= $bH ?>h <?= $bM > 0 ? $bM . 'm' : '00m' ?></strong></td>
                  <td class="muted"><?= $nH ?>h <?= $nM > 0 ? $nM . 'm' : '00m' ?></td>
                  <td><?= number_format((float)$p['hourly_rate'], 0) ?> €/h</td>
                  <td><strong style="color:var(--success);"><?= number_format((float)$p['revenue'], 0, ',', ' ') ?> €</strong></td>
                  <td><span class="status-badge <?= $p['project_status'] ?>"><?= match($p['project_status']) {'active'=>'Active','on_hold'=>'On Hold','completed'=>'Completed',default=>$p['project_status']} ?></span></td>
                </tr>
              <?php endforeach; ?>
              <?php if (!empty($projects)): ?>
                <tr>
                  <td colspan="4" style="font-weight:700;padding-top:14px;">Celkom</td>
                  <td style="font-weight:800;font-size:15px;color:var(--text-primary);padding-top:14px;"><?= number_format($totalRevenue, 0, ',', ' ') ?> €</td>
                  <td style="padding-top:14px;"></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php if ($totals['total_minutes'] > 0): ?>
        <div class="section-block">
          <div class="section-block-header">
            <span class="section-block-title">Billable vs Non-billable</span>
          </div>
          <div class="billable-split-wrap">
            <div class="billable-split-row">
              <div class="billable-split-item">
                <div class="billable-split-dot green"></div>
                <div class="billable-split-info">
                  <span class="billable-split-time"><?= $totalBillH ?>h <?= $totalBillM > 0 ? $totalBillM . 'm' : '00m' ?></span>
                  <span class="billable-split-lbl">Billable</span>
                </div>
                <span class="billable-split-pct"><?= $billablePercent ?>%</span>
              </div>
              <div class="billable-split-divider"></div>
              <div class="billable-split-item">
                <div class="billable-split-dot gray"></div>
                <div class="billable-split-info">
                  <span class="billable-split-time"><?= $totalNonH ?>h <?= $totalNonM > 0 ? $totalNonM . 'm' : '00m' ?></span>
                  <span class="billable-split-lbl">Non-billable</span>
                </div>
                <span class="billable-split-pct"><?= 100 - $billablePercent ?>%</span>
              </div>
            </div>
            <div class="billable-combined-bar">
              <div class="billable-combined-fill" style="width:<?= $billablePercent ?>%;"></div>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<?php require template_path('partials/footer.php'); ?>
