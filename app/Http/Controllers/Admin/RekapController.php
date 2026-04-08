<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $reports   = Report::with('user')->latest()->get();

        return view('admin.rekap', compact(
            'totalReports', 'menunggu', 'diproses', 'selesai',
            'reports', 'period'
        ) + $chartData);
    }

    /**
     * Export tabel laporan ke CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $reports = Report::with('user')->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rekap_laporan_' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($reports) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'ID', 'Nama Pelapor', 'Email', 'Deskripsi',
                'Latitude', 'Longitude', 'Status', 'Tanggal Dibuat'
            ]);

            foreach ($reports as $report) {
                fputcsv($handle, [
                    $report->id,
                    $report->user->name,
                    $report->user->email,
                    $report->deskripsi,
                    $report->latitude,
                    $report->longitude,
                    $report->status,
                    $report->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
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
