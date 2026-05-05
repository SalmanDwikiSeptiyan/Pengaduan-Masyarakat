<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RekapController extends Controller
{
    /**
     * Halaman rekap laporan (print-friendly).
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');

        $totalReports = Report::count();
        $menunggu     = Report::where('status', 'Menunggu')->count();
        $diproses     = Report::where('status', 'Diproses')->count();
        $selesai      = Report::where('status', 'Selesai')->count();

        $chartData = $this->getChartData($period);

        // Apply filters to preview table
        $query = Report::with('user')->latest();

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        $reports = $query->get();

        return view('admin.rekap', compact(
            'totalReports', 'menunggu', 'diproses', 'selesai',
            'reports', 'period'
        ) + $chartData);
    }

    /**
     * Export laporan sebagai halaman cetak (print-friendly report).
     */
    public function exportReport(Request $request)
    {
        $query = Report::with('user')->latest();

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        $reports = $query->get();

        $totalReports = $reports->count();
        $menunggu     = $reports->where('status', 'Menunggu')->count();
        $diproses     = $reports->where('status', 'Diproses')->count();
        $selesai      = $reports->where('status', 'Selesai')->count();

        return view('admin.rekap-print', compact(
            'reports', 'totalReports', 'menunggu', 'diproses', 'selesai'
        ))->with([
            'startDate' => $request->start_date,
            'endDate'   => $request->end_date,
            'status'    => $request->status ?? 'Semua',
        ]);
    }

    private function getChartData(string $period): array
    {
        $chartLabels = [];
        $chartCounts = [];

        switch ($period) {
            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $chartLabels[] = $date->format('M Y');
                    $chartCounts[] = Report::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)->count();
                }
                break;

            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $chartLabels[] = $date->format('d M');
                    $chartCounts[] = Report::whereDate('created_at', $date->toDateString())->count();
                }
                break;

            case 'month':
            default:
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $chartLabels[] = $date->format('d M');
                    $chartCounts[] = Report::whereDate('created_at', $date->toDateString())->count();
                }
                break;
        }

        return compact('chartLabels', 'chartCounts');
    }
}
