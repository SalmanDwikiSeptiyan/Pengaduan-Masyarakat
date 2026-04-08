<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;

class MapController extends Controller
{
    /**
     * Peta sebaran seluruh laporan.
     */
    public function index()
    {
        $reports = Report::with('user')
            ->select('id', 'user_id', 'deskripsi', 'latitude', 'longitude', 'status', 'created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id'        => $r->id,
                    'user'      => $r->user->name,
                    'deskripsi' => \Illuminate\Support\Str::limit($r->deskripsi, 50),
                    'lat'       => (float) $r->latitude,
                    'lng'       => (float) $r->longitude,
                    'status'    => $r->status,
                    'tanggal'   => $r->created_at->format('d M Y'),
                    'url'       => route('admin.reports.show', $r->id),
                ];
            });

        $countAll      = $reports->count();
        $countMenunggu = $reports->where('status', 'Menunggu')->count();
        $countDiproses = $reports->where('status', 'Diproses')->count();
        $countSelesai  = $reports->where('status', 'Selesai')->count();

        return view('admin.map', compact(
            'reports', 'countAll', 'countMenunggu', 'countDiproses', 'countSelesai'
        ));
    }
}
