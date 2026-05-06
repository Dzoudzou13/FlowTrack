<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Billing | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

$projects = $projects ?? [];
$totals   = $totals   ?? ['total_minutes' => 0, 'billable_minutes' => 0, 'revenue' => 0];
$month    = $month    ?? date('Y-m');

$totalBillH   = intdiv((int) $totals['billable_minutes'], 60);
$totalBillM   = (int) $totals['billable_minutes'] % 60;
$totalNonH    = intdiv(max(0, (int)$totals['total_minutes'] - (int)$totals['billable_minutes']), 60);
$totalNonM    = max(0, (int)$totals['total_minutes'] - (int)$totals['billable_minutes']) % 60;
$totalRevenue = (float) $totals['revenue'];
$billablePercent = $totals['total_minutes'] > 0 ? round((int)$totals['billable_minutes'] / (int)$totals['total_minutes'] * 100) : 0;

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
          <input type="month" class="filter-select" value="<?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>"
            onchange="window.location='<?= htmlspecialchars(app_url('/billing'), ENT_QUOTES, 'UTF-8') ?>?month='+this.value" />
        </div>
      </div>

      <!-- Summary cards -->
      <div class="billing-grid">
        <div class="billing-card">
          <div class="billing-card-label">Celkové tržby</div>
          <div class="billing-card-value"><?= number_format($totalRevenue, 0, ',', ' ') ?> €</div>
          <div class="billing-card-sub">za <?= date('F Y', strtotime($month . '-01')) ?></div>
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
          <div class="section-block-body">
            <div style="padding:20px 20px 10px;">
              <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px;">
                <div style="flex:1;">
                  <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12.5px;color:var(--text-secondary);"><span class="billable-dot yes" style="display:inline-block;"></span> Billable (<?= $totalBillH ?>h)</span>
                    <span style="font-size:12.5px;font-weight:700;color:var(--text-primary);"><?= $billablePercent ?>%</span>
                  </div>
                  <div class="progress-bar-wrap"><div class="progress-bar-fill green" style="width:<?= $billablePercent ?>%;"></div></div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:14px;">
                <div style="flex:1;">
                  <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:12.5px;color:var(--text-secondary);"><span class="billable-dot no" style="display:inline-block;"></span> Non-billable (<?= $totalNonH ?>h)</span>
                    <span style="font-size:12.5px;font-weight:700;color:var(--text-primary);"><?= 100 - $billablePercent ?>%</span>
                  </div>
                  <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:<?= 100 - $billablePercent ?>%;background:var(--border);"></div></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<?php require template_path('partials/footer.php'); ?>
