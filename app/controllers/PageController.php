<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class PageController extends Controller
{
    public function home(): void
    {
        $this->render('home/index');
    }

    public function projects(): void
    {
        // Zobrazi zoznam projektov.
        $this->render('projects/index');
    }

    public function projectCreate(): void
    {
        // Zobrazi formular projektu.
        $this->render('projects/create');
    }

    public function projectShow(string $id): void
    {
        // Zobrazi detail projektu.
        $this->render('projects/show', [
            'projectId' => $id,
        ]);
    }

    public function projectEdit(string $id): void
    {
        // Zobrazi upravu projektu.
        $this->render('projects/edit', [
            'projectId' => $id,
        ]);
    }

    public function projectBoard(string $id = '1'): void
    {
        // Zobrazi kanban board.
        $this->render('projects/board', [
            'projectId' => $id,
        ]);
    }

    public function taskShow(string $id): void
    {
        // Zobrazi detail tasku.
        $this->render('tasks/show', [
            'taskId' => $id,
        ]);
    }

    public function time(): void
    {
        // Zobrazi time tracking.
        $this->render('time/index');
    }

    public function billing(): void
    {
        // Zobrazi billing.
        $this->render('billing/index');
    }

    public function team(): void
    {
        // Zobrazi tim.
        $this->render('team/index');
    }

    public function activity(): void
    {
        // Zobrazi activity log.
        $this->render('activity/index');
    }

    public function board(): void
    {
        // Zobrazi globálny kanban board.
        $this->render('board/index');
    }

    public function settings(): void
    {
        // Zobrazi nastavenia.
        $this->render('settings/index');
    }
}
