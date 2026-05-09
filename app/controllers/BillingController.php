<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TimeEntryModel;

final class BillingController extends Controller
{
    public function index(): void
    {
        auth_guard();
        $user = auth_user();

        $type = $_GET['type'] ?? 'month';
        if (!in_array($type, ['week', 'month', 'year'], true)) {
            $type = 'month';
        }

        $period = $_GET['period'] ?? match ($type) {
            'week'  => date('Y-m-d', strtotime('monday this week')),
            'year'  => date('Y'),
            default => date('Y-m'),
        };

        if ($type === 'week') {
            $ts          = strtotime($period);
            $prevPeriod  = date('Y-m-d', strtotime('-1 week', $ts));
            $nextPeriod  = date('Y-m-d', strtotime('+1 week', $ts));
            $periodLabel = 'Týždeň ' . date('W', $ts) . ', ' . date('Y', $ts);
        } elseif ($type === 'year') {
            $prevPeriod  = (string)((int)$period - 1);
            $nextPeriod  = (string)((int)$period + 1);
            $periodLabel = $period;
        } else {
            $ts          = strtotime($period . '-01');
            $prevPeriod  = date('Y-m', strtotime('-1 month', $ts));
            $nextPeriod  = date('Y-m', strtotime('+1 month', $ts));
            $periodLabel = date('F Y', $ts);
        }

        $projects = TimeEntryModel::billingByProject((int) $user['workspace_id'], $period, $type);
        $totals   = TimeEntryModel::sumByWorkspace((int) $user['workspace_id'], $period, $type);

        $this->render('billing/index', [
            'pageTitle'   => 'Billing | FlowTrack',
            'projects'    => $projects,
            'totals'      => $totals,
            'period'      => $period,
            'type'        => $type,
            'prevPeriod'  => $prevPeriod,
            'nextPeriod'  => $nextPeriod,
            'periodLabel' => $periodLabel,
        ]);
    }
}
