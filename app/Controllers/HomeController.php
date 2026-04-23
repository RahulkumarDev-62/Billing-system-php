<?php

declare(strict_types=1);

namespace App\Controllers;

final class HomeController extends BaseController
{
    public function index(): string
    {
        if (auth_check()) {
            redirect(role_dashboard_path());
        }

        return $this->view('home/landing', [
            'title' => 'Supermarket Management System',
        ], null);
    }
}
