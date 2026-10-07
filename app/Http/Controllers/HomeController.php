<?php

namespace App\Http\Controllers;

use App\Support\DashboardAgenda;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Affiche le tableau de bord principal avec les statistiques.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user?->isComptable() && ! $user->isSuperAdmin()) {
            return view('user.home-comptable', [
                'comptableStats' => DashboardAgenda::buildComptableStats(),
            ]);
        }

        return view('user.home', DashboardAgenda::build($user, 'home'));
    }
}
