<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Daftar semua laporan.
     */
    public function index()
    {
        $reports = Report::with('user')->latest()->get();

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Detail laporan.
     */
    public function show($id)
    {
        $report = Report::with('user')->findOrFail($id);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Update status laporan (Menunggu -> Diproses).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Selesai',
        ]);

        $report = Report::findOrFail($id);
        $report->update(['status' => $request->status]);

        // Buat notifikasi otomatis
        Notification::create([
            'user_id'   => $report->user_id,
            'report_id' => $report->id,
            'message'   => 'Status laporan #' . $report->id . ' diubah menjadi ' . $request->status,
            'is_read'   => false,
        ]);

        return redirect()->route('admin.reports.show', $id)
            ->with('success', 'Status laporan berhasil diperbarui menjadi ' . $request->status);
    }

    /**
     * Selesaikan laporan dengan upload foto after.
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

        // Buat notifikasi otomatis
        Notification::create([
            'user_id'   => $report->user_id,
            'report_id' => $report->id,
            'message'   => 'Laporan Anda telah selesai ditangani. Terima kasih atas laporan Anda.',
            'is_read'   => false,
        ]);

        return redirect()->route('admin.reports.show', $id)
            ->with('success', 'Laporan berhasil diselesaikan!');
    }
}
