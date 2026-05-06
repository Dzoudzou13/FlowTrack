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
        $user  = auth_user();
        $month = $_GET['month'] ?? date('Y-m');

        $projects = TimeEntryModel::billingByProject((int) $user['workspace_id'], $month);
        $totals   = TimeEntryModel::sumByWorkspace((int) $user['workspace_id'], $month);

        $this->render('billing/index', [
            'pageTitle' => 'Billing | FlowTrack',
            'projects'  => $projects,
            'totals'    => $totals,
            'month'     => $month,
        ]);
    }
}
