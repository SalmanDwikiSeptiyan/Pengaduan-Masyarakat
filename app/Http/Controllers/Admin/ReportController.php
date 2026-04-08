<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Daftar semua laporan dengan search, filter & pagination.
     */
    public function index(Request $request)
    {
        $query = Report::with('user');

        // Search by user name or deskripsi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        // Count per status (before pagination, after search)
        $baseQuery = clone $query;
        $countAll      = (clone $baseQuery)->count();
        $countMenunggu = (clone $baseQuery)->where('status', 'Menunggu')->count();
        $countDiproses = (clone $baseQuery)->where('status', 'Diproses')->count();
        $countSelesai  = (clone $baseQuery)->where('status', 'Selesai')->count();

        $reports = $query->latest()->paginate(10)->withQueryString();

        return view('admin.reports.index', compact(
            'reports', 'countAll', 'countMenunggu', 'countDiproses', 'countSelesai'
        ));
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

    /**
     * Hapus laporan (foto tidak sesuai / spam).
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        // Hapus file foto dari storage
        if ($report->foto_before && \Storage::disk('public')->exists($report->foto_before)) {
            \Storage::disk('public')->delete($report->foto_before);
        }
        if ($report->foto_after && \Storage::disk('public')->exists($report->foto_after)) {
            \Storage::disk('public')->delete($report->foto_after);
        }

        // Hapus notifikasi terkait
        \App\Models\Notification::where('report_id', $report->id)->delete();

        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Laporan #' . $id . ' berhasil dihapus.');
    }
}
