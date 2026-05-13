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
        $this->render('projects/index');
    }

    public function projectCreate(): void
    {
        $this->render('projects/create');
    }

    public function projectShow(string $id): void
    {
        $this->render('projects/show', [
            'projectId' => $id,
        ]);
    }

    public function projectEdit(string $id): void
    {
        $this->render('projects/edit', [
            'projectId' => $id,
        ]);
    }

    public function projectBoard(string $id = '1'): void
    {
        $this->render('projects/board', [
            'projectId' => $id,
        ]);
    }

    public function taskShow(string $id): void
    {
        $this->render('tasks/show', [
            'taskId' => $id,
        ]);
    }

    public function time(): void
    {
        $this->render('time/index');
    }

    public function billing(): void
    {
        $this->render('billing/index');
    }

    public function team(): void
    {
        $this->render('team/index');
    }

    public function activity(): void
    {
        $this->render('activity/index');
    }

    public function board(): void
    {
        $this->render('board/index');
    }

    public function settings(): void
    {
        $this->render('settings/index');
    }
}
