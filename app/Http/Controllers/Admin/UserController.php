<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Daftar semua user (non-admin) dengan search & pagination.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user')->withCount('reports');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $totalUsers   = User::where('role', 'user')->count();
        $users        = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'totalUsers'));
    }

    /**
     * Detail user + semua laporan miliknya.
     */
    public function show($id)
    {
        $user = User::where('role', 'user')->withCount('reports')->findOrFail($id);

        $reports = Report::where('user_id', $id)
            ->latest()
            ->get();

        $countMenunggu = $reports->where('status', 'Menunggu')->count();
        $countDiproses = $reports->where('status', 'Diproses')->count();
        $countSelesai  = $reports->where('status', 'Selesai')->count();

        return view('admin.users.show', compact(
            'user', 'reports', 'countMenunggu', 'countDiproses', 'countSelesai'
        ));
    }
}
