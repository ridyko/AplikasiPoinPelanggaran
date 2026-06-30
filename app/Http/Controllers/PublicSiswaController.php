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

    public function showLengkapiDataForm(Request $request)
    {
        $nis = $request->query('nis');
        $student = null;

        if ($nis) {
            $request->validate([
                'nis' => 'required|numeric|digits:6',
            ], [
                'nis.digits' => 'NIS harus terdiri dari 6 digit angka.',
                'nis.numeric' => 'NIS harus berupa angka.',
            ]);

            $student = Student::with('kelas.major')->where('nis', $nis)->first();

            if (!$student) {
                return redirect()->route('siswa.lengkapi_data')
                    ->with('error', 'Siswa dengan NIS ' . $nis . ' tidak ditemukan. Periksa kembali NIS Anda.')
                    ->withInput();
            }
        }

        return view('siswa.lengkapi_data', compact('student', 'nis'));
    }

    public function submitLengkapiData(Request $request)
    {
        $request->validate([
            'nis' => 'required|numeric|digits:6|exists:students,nis',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|min:10|max:15|regex:/^08[0-9]{8,13}$/',
        ], [
            'nis.exists' => 'Data siswa tidak terdaftar.',
            'parent_name.required' => 'Nama wali wajib diisi.',
            'parent_phone.required' => 'No WhatsApp wali wajib diisi.',
            'parent_phone.regex' => 'Format nomor WhatsApp harus diawali dengan 08 (contoh: 08123456789).',
            'parent_phone.min' => 'Nomor WhatsApp minimal 10 digit.',
            'parent_phone.max' => 'Nomor WhatsApp maksimal 15 digit.',
        ]);

        $student = Student::with('kelas')->where('nis', $request->input('nis'))->firstOrFail();
        $student->update([
            'parent_name' => $request->input('parent_name'),
            'parent_phone' => $request->input('parent_phone'),
        ]);

        return redirect()->route('siswa.lengkapi_data')
            ->with('success', 'Data kontak orang tua/wali untuk ' . $student->name . ' (' . $student->kelas->class_name . ') berhasil disimpan. Terima kasih!');
    }
}
