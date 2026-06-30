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
            'nis' => 'required|numeric|digits:6',
        ]);

        $nis = $request->input('nis');
        
        $student = Student::with(['kelas.major', 'violationLogs.violation', 'violationLogs.user'])
            ->where('nis', $nis)
            ->first();

        if (!$student) {
            return back()->with('error', 'Siswa dengan NIS tersebut tidak ditemukan. Periksa kembali NIS 6 digit yang dimasukkan.')->withInput();
        }

        return view('siswa.details', compact('student'));
    }
}
