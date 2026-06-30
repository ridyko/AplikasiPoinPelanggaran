<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Kelas;
use App\Models\Major;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isBkOrAbove()) {
            return redirect()->route('dashboard')->with('error', 'Hanya Guru BK, Wakil Kesiswaan, atau Super Admin yang memiliki akses ke halaman ini.');
        }

        $classId = $request->input('class_id');
        $search = $request->input('search');
        $tahunAjaran = $request->input('tahun_ajaran');

        $query = Student::with(['kelas.major']);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($tahunAjaran) {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->get();
        $classes = Kelas::with('major')->orderBy('class_name')->get();
        
        // Fetch all unique academic years in the database for the filter dropdown
        $tahunAjaranList = Student::distinct()->pluck('tahun_ajaran')->filter()->values();
        
        return view('students.index', compact('students', 'classes', 'classId', 'search', 'tahunAjaran', 'tahunAjaranList'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke tindakan ini.');
        }

        $validated = $request->validate([
            'nis' => 'required|numeric|digits:6|unique:students,nis',
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        $validated['status'] = 'aktif';

        Student::create($validated);

        return redirect()->route('students')->with('success', 'Siswa baru berhasil ditambahkan!');
    }

    public function show(Student $student)
    {
        $user = auth()->user();

        // Wali Kelas hanya bisa lihat detail siswa di kelasnya
        if ($user->isWaliKelas()) {
            $classIds = $user->classes->pluck('id')->toArray();
            if (!in_array($student->class_id, $classIds)) {
                return redirect()->route('dashboard')->with('error', 'Anda hanya dapat melihat detail siswa di kelas Anda.');
            }
        } elseif (!$user->isBkOrAbove()) {
            // Guru biasa tidak bisa akses detail siswa
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Eager load the required relationships for detailed view
        $student->load(['kelas.major', 'violationLogs.violation', 'violationLogs.user']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke halaman ini.');
        }

        $classes = Kelas::with('major')->orderBy('class_name')->get();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke tindakan ini.');
        }

        $validated = $request->validate([
            'nis' => 'required|numeric|digits:6|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,skorsing,drop_out,lulus',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        $student->update($validated);

        return redirect()->route('students')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Student $student)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke tindakan ini.');
        }

        $student->delete();

        return redirect()->route('students')->with('success', 'Data siswa berhasil dihapus!');
    }

    public function bulkAction(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke tindakan ini.');
        }

        $validated = $request->validate([
            'tahun_ajaran' => 'required|string',
            'action' => 'required|in:lulus,hapus',
        ]);

        $tahun = $validated['tahun_ajaran'];
        $action = $validated['action'];

        if ($action === 'lulus') {
            Student::where('tahun_ajaran', $tahun)->update(['status' => 'lulus']);
            return redirect()->route('students')->with('success', "Semua siswa tahun ajaran {$tahun} berhasil ditandai sebagai Lulus!");
        } elseif ($action === 'hapus') {
            // Delete will cascade delete violation logs because of foreign keys onDelete('cascade')
            Student::where('tahun_ajaran', $tahun)->delete();
            return redirect()->route('students')->with('success', "Semua data siswa tahun ajaran {$tahun} berhasil dihapus permanen!");
        }

        return redirect()->route('students');
    }

    public function downloadTemplate()
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke halaman ini.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Nama Siswa');
        $sheet->setCellValue('C1', 'Kelas');
        $sheet->setCellValue('D1', 'Tahun Ajaran');
        $sheet->setCellValue('E1', 'Nama Wali');
        $sheet->setCellValue('F1', 'No WhatsApp Wali');
        
        // Example Row
        $sheet->setCellValue('A2', '990001');
        $sheet->setCellValue('B2', 'Aditya Pratama');
        $sheet->setCellValue('C2', 'XII RPL 1');
        $sheet->setCellValue('D2', '2025/2026');
        $sheet->setCellValue('E2', 'Hartono');
        $sheet->setCellValue('F2', '081234567890');

        // Autofit columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_siswa.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function importExcel(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke tindakan ini.');
        }

        $request->validate([
            'file' => 'required|extensions:xlsx,xls,csv|max:5120',
        ]);

        $file = $request->file('file');
        
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
            
            // Validate header row
            $header = $rows[1] ?? null;
            unset($rows[1]); // Remove header but preserve index keys (2, 3, ...) for accurate error reporting
            
            $errors = [];
            $successCount = 0;
            $updatedCount = 0;

            // Cache classes to avoid querying in loop
            $classesMap = Kelas::all()->pluck('id', 'class_name')->mapWithKeys(function ($id, $name) {
                return [strtolower(trim($name)) => $id];
            })->toArray();

            foreach ($rows as $rowIndex => $row) {
                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $nis = trim($row['A'] ?? '');
                $name = trim($row['B'] ?? '');
                $className = trim($row['C'] ?? '');
                $tahunAjaran = trim($row['D'] ?? '');
                $parentName = trim($row['E'] ?? '');
                $parentPhone = trim($row['F'] ?? '');

                $rowErrors = [];

                if (!$nis) {
                    $rowErrors[] = "NIS wajib diisi.";
                } elseif (!is_numeric($nis)) {
                    $rowErrors[] = "NIS harus berupa angka.";
                } elseif (strlen($nis) !== 6) {
                    $rowErrors[] = "NIS harus terdiri dari 6 digit angka.";
                }

                if (!$name) {
                    $rowErrors[] = "Nama Siswa wajib diisi.";
                }

                if (!$className) {
                    $rowErrors[] = "Kelas wajib diisi.";
                } else {
                    $classKey = strtolower($className);
                    if (!isset($classesMap[$classKey])) {
                        $rowErrors[] = "Kelas '{$className}' tidak terdaftar di sistem.";
                    }
                }

                if (!$tahunAjaran) {
                    $rowErrors[] = "Tahun Ajaran wajib diisi.";
                }

                if (count($rowErrors) > 0) {
                    $errors[] = "Baris {$rowIndex}: " . implode(' ', $rowErrors);
                    continue;
                }

                // If no errors, process database save/update
                $classId = $classesMap[strtolower($className)];

                $student = Student::where('nis', $nis)->first();

                if ($student) {
                    $student->update([
                        'name' => $name,
                        'class_id' => $classId,
                        'tahun_ajaran' => $tahunAjaran,
                        'parent_name' => $parentName ?: null,
                        'parent_phone' => $parentPhone ?: null,
                    ]);
                    $updatedCount++;
                } else {
                    Student::create([
                        'nis' => $nis,
                        'name' => $name,
                        'class_id' => $classId,
                        'tahun_ajaran' => $tahunAjaran,
                        'parent_name' => $parentName ?: null,
                        'parent_phone' => $parentPhone ?: null,
                        'current_points' => 0,
                        'status' => 'aktif',
                    ]);
                    $successCount++;
                }
            }

            if (count($errors) > 0) {
                $errorMsg = "Import selesai dengan beberapa error:<br>" . implode('<br>', $errors);
                return redirect()->route('students')
                    ->with('success', "Berhasil menambahkan {$successCount} siswa baru dan memperbarui {$updatedCount} siswa.")
                    ->with('error', $errorMsg);
            }

            return redirect()->route('students')->with('success', "Berhasil mengimpor data siswa! (Baru: {$successCount}, Diperbarui: {$updatedCount})");

        } catch (\Exception $e) {
            return redirect()->route('students')->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
}
