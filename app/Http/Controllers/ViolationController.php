<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke halaman ini.');
        }

        $violations = Violation::orderBy('category')->orderBy('points')->get();
        return view('violations.index', compact('violations'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke tindakan ini.');
        }

        $validated = $request->validate([
            'violation_name' => 'required|string|max:255',
            'category' => 'required|in:ringan,sedang,berat',
            'points' => 'required|integer|min:1',
        ]);

        Violation::create($validated);

        return redirect()->route('violations.index')->with('success', 'Jenis pelanggaran baru berhasil ditambahkan!');
    }

    public function edit(Violation $violation)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke halaman ini.');
        }

        return view('violations.edit', compact('violation'));
    }

    public function update(Request $request, Violation $violation)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke tindakan ini.');
        }

        $validated = $request->validate([
            'violation_name' => 'required|string|max:255',
            'category' => 'required|in:ringan,sedang,berat',
            'points' => 'required|integer|min:1',
        ]);

        $violation->update($validated);

        return redirect()->route('violations.index')->with('success', 'Jenis pelanggaran berhasil diperbarui!');
    }

    public function destroy(Violation $violation)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke tindakan ini.');
        }

        // Check if there are violation logs associated with this violation type
        if ($violation->violationLogs()->exists()) {
            return redirect()->route('violations.index')->with('error', 'Tidak dapat menghapus jenis pelanggaran ini karena sudah dicatat pada riwayat pelanggaran siswa.');
        }

        $violation->delete();

        return redirect()->route('violations.index')->with('success', 'Jenis pelanggaran berhasil dihapus!');
    }
}
