<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Violation;
use App\Models\ViolationLog;
use App\Events\ViolationLogged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViolationLogController extends Controller
{
    public function create(Request $request)
    {
        $user = auth()->user();
        $selectedStudentId = $request->query('student_id');

        // Semua role (termasuk Wali Kelas) dapat mencatat pelanggaran untuk semua siswa aktif
        $students = Student::with('kelas')->where('status', 'aktif')->orderBy('name')->get();

        $violations = Violation::orderBy('category')->orderBy('points')->get();
        return view('violations.create', compact('students', 'violations', 'selectedStudentId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_id' => 'required|exists:violations,id',
            'date_occurred' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $violation = Violation::findOrFail($validated['violation_id']);
        $student = Student::findOrFail($validated['student_id']);

        DB::beginTransaction();
        try {
            // Create violation log
            $log = ViolationLog::create([
                'student_id' => $validated['student_id'],
                'violation_id' => $validated['violation_id'],
                'points_added' => $violation->points,
                'date_occurred' => $validated['date_occurred'],
                'description' => $validated['description'],
                'user_id' => auth()->id(),
            ]);

            // Update student points
            $student->increment('current_points', $violation->points);

            // Determine if student status needs to change (e.g. if >= 100 points, update status to drop out or skorsing)
            if ($student->current_points >= 100) {
                $student->update(['status' => 'drop_out']);
            } elseif ($student->current_points >= 50) {
                $student->update(['status' => 'skorsing']);
            }

            DB::commit();

            // Fire event to send WhatsApp notification
            event(new ViolationLogged($log));

            return redirect()->route('dashboard')->with('success', "Pelanggaran berhasil dicatat untuk siswa {$student->name}! Notifikasi WhatsApp sedang dikirim ke orang tua.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mencatat pelanggaran: ' . $e->getMessage())->withInput();
        }
    }
}
