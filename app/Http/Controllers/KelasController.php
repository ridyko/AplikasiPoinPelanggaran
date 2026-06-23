<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display list of classes.
     */
    public function index()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Hanya Super Admin yang dapat mengelola kelas.');
        }

        $kelas   = Kelas::with(['major', 'homeroomTeacher'])
                        ->withCount('students')
                        ->orderBy('class_name')
                        ->get();
        $majors  = Major::orderBy('name')->get();
        $waliKandidates = User::where('role', 'wali_kelas')
                              ->orderBy('name')
                              ->get();

        return view('kelas.index', compact('kelas', 'majors', 'waliKandidates'));
    }

    /**
     * Store a newly created class.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $validated = $request->validate([
            'class_name'          => 'required|string|max:50|unique:classes,class_name',
            'major_id'            => 'required|exists:majors,id',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ], [
            'class_name.required' => 'Nama kelas wajib diisi.',
            'class_name.unique'   => 'Nama kelas sudah ada.',
            'major_id.required'   => 'Jurusan wajib dipilih.',
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Update the specified class.
     */
    public function update(Request $request, Kelas $kelas)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $validated = $request->validate([
            'class_name'          => 'required|string|max:50|unique:classes,class_name,' . $kelas->id,
            'major_id'            => 'required|exists:majors,id',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ], [
            'class_name.required' => 'Nama kelas wajib diisi.',
            'class_name.unique'   => 'Nama kelas sudah ada.',
            'major_id.required'   => 'Jurusan wajib dipilih.',
        ]);

        $kelas->update($validated);

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Delete the specified class.
     */
    public function destroy(Kelas $kelas)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        // Safety check: cannot delete class that has students
        if ($kelas->students()->count() > 0) {
            return redirect()->route('kelas.index')
                ->with('error', 'Kelas "' . $kelas->class_name . '" tidak dapat dihapus karena masih memiliki ' . $kelas->students()->count() . ' siswa terdaftar.');
        }

        $className = $kelas->class_name;
        $kelas->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas "' . $className . '" berhasil dihapus.');
    }
}
