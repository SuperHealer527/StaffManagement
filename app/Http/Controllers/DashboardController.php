<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User $user */
        $user = request()->user();
        $template = match ($user->role) {
            'staff', 'temporary', 'trainer' => 'Staff',
            'semi_employee' => 'SubMain',
            default => 'Main',
        };

        return view("static-pages.{$template}");
    }
}
