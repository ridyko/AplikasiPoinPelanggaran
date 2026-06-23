<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class PublicSiswaController extends Controller
{
    public function showCheckForm()
    {
        return view('siswa.check');
    }

    public function check(Request $request)
    {
        $request->validate([
            'nisn' => 'required|numeric',
        ]);

        $nisn = $request->input('nisn');
        
        $student = Student::with(['kelas.major', 'violationLogs.violation', 'violationLogs.user'])
            ->where('nisn', $nisn)
            ->first();

        if (!$student) {
            return back()->with('error', 'Siswa dengan NISN tersebut tidak ditemukan. Periksa kembali NISN yang dimasukkan.')->withInput();
        }

        return view('siswa.details', compact('student'));
    }
}
