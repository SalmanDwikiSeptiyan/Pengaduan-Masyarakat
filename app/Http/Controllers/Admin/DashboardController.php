<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalReports   = Report::count();
        $menunggu       = Report::where('status', 'Menunggu')->count();
        $diproses       = Report::where('status', 'Diproses')->count();
        $selesai        = Report::where('status', 'Selesai')->count();
        $totalUsers     = User::where('role', 'user')->count();
        $recentReports  = Report::with('user')->latest()->take(5)->get();

        // Period filter: week (default), month, year
        $period = $request->get('period', 'week');
        $chartData = $this->getChartData($period);

        return view('admin.dashboard', compact(
            'totalReports', 'menunggu', 'diproses', 'selesai',
            'totalUsers', 'recentReports',
            'period'
        ) + $chartData);
    }

    private function getChartData(string $period): array
    {
        $chartLabels = [];
        $chartCounts = [];

        switch ($period) {
            case 'month':
                // 30 hari terakhir, grouped per 5 hari
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $chartLabels[] = $date->format('d M');
                    $chartCounts[] = Report::whereDate('created_at', $date->toDateString())->count();
                }
                break;

            case 'year':
                // 12 bulan terakhir
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $chartLabels[] = $date->format('M Y');
                    $chartCounts[] = Report::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                }
                break;

            case 'week':
            default:
                // 7 hari terakhir
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $chartLabels[] = $date->format('d M');
                    $chartCounts[] = Report::whereDate('created_at', $date->toDateString())->count();
                }
                break;
        }

        return compact('chartLabels', 'chartCounts');
    }
}
