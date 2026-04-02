<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalReports   = Report::count();
        $menunggu       = Report::where('status', 'Menunggu')->count();
        $diproses       = Report::where('status', 'Diproses')->count();
        $selesai        = Report::where('status', 'Selesai')->count();
        $totalUsers     = User::where('role', 'user')->count();
        $recentReports  = Report::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalReports', 'menunggu', 'diproses', 'selesai', 'totalUsers', 'recentReports'
        ));
    }
}
