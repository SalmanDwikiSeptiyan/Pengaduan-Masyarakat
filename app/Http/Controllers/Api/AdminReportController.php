<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * GET /api/admin/reports
     * Admin melihat semua laporan.
     */
    public function index()
    {
        $reports = Report::with('user')->latest()->get();

        return response()->json([
            'message' => 'Semua laporan',
            'reports' => $reports,
        ]);
    }

    /**
     * GET /api/admin/reports/{id}
     * Admin melihat detail laporan.
     */
    public function show($id)
    {
        $report = Report::with('user')->findOrFail($id);

        return response()->json([
            'message' => 'Detail laporan',
            'report'  => $report,
        ]);
    }

    /**
     * PUT /api/admin/reports/{id}
     * Admin memperbarui status laporan (misal: Menunggu -> Diproses).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Selesai',
        ]);

        $report = Report::findOrFail($id);
        $report->update([
            'status' => $request->status,
        ]);

        // Kirim notifikasi ke user pemilik laporan
        Notification::create([  
            'user_id'   => $report->user_id,
            'report_id' => $report->id,
            'message'   => 'Status laporan #' . $report->id . ' diubah menjadi ' . $request->status,
            'is_read'   => false,
        ]);

        return response()->json([
            'message' => 'Status laporan berhasil diperbarui',
            'report'  => $report->load('user'),
        ]);
    }

    /**
     * POST /api/admin/reports/{id}/complete
     * Admin menyelesaikan laporan (upload foto_after, set status Selesai).
     */
    public function complete(Request $request, $id)
    {
        $request->validate([
            'foto_after' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $report = Report::findOrFail($id);

        $fotoPath = $request->file('foto_after')->store('reports', 'public');

        $report->update([
            'foto_after' => $fotoPath,
            'status'     => 'Selesai',
        ]);

        // Kirim notifikasi ke user pemilik laporan
        Notification::create([
            'user_id'   => $report->user_id,
            'report_id' => $report->id,
            'message'   => 'Laporan #' . $report->id . ' telah diselesaikan',
            'is_read'   => false,
        ]);

        return response()->json([
            'message' => 'Laporan berhasil diselesaikan',
            'report'  => $report->load('user'),
        ]);
    }
}
