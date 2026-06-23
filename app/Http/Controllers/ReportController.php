<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Kelas;
use App\Models\ViolationLog;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
    /**
     * Display reports filter form (Admin/BK only).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->isWaliKelas()) {
            abort(403, 'Akses ditolak. Wali Kelas hanya dapat mengunduh rekap kelas langsung dari dashboard.');
        }
        if ($user->role === 'guru') {
            abort(403, 'Akses ditolak. Guru tidak memiliki akses ke halaman laporan.');
        }

        $classes = Kelas::orderBy('class_name', 'asc')->get();
        
        $classId = $request->input('class_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = ViolationLog::with(['student.kelas', 'violation', 'user']);

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($startDate) {
            $query->whereDate('date_occurred', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date_occurred', '<=', $endDate);
        }

        // If no filter is applied, show recent 20 logs
        if (!$classId && !$startDate && !$endDate) {
            $logs = $query->orderBy('date_occurred', 'desc')->limit(20)->get();
            $isFiltered = false;
        } else {
            $logs = $query->orderBy('date_occurred', 'desc')->get();
            $isFiltered = true;
        }

        return view('reports.index', compact('classes', 'logs', 'classId', 'startDate', 'endDate', 'isFiltered'));
    }

    /**
     * Export violation logs to Excel file.
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $classId = $request->input('class_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Security scoping for Wali Kelas
        if ($user->isWaliKelas()) {
            $myClass = Kelas::where('homeroom_teacher_id', $user->id)->first();
            if (!$myClass) {
                return back()->with('error', 'Anda tidak terdaftar sebagai Wali Kelas di kelas manapun.');
            }
            $classId = $myClass->id;
        }

        // Build query
        $query = ViolationLog::with(['student.kelas', 'violation', 'user']);

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($startDate) {
            $query->whereDate('date_occurred', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date_occurred', '<=', $endDate);
        }

        $logs = $query->orderBy('date_occurred', 'desc')->get();

        // Check if logs exist
        if ($logs->isEmpty()) {
            return back()->with('error', 'Tidak ada data pelanggaran ditemukan untuk kriteria filter tersebut.');
        }

        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pelanggaran');

        // Styles
        $titleStyle = [
            'font' => [
                'bold' => true,
                'size' => 16,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $subtitleStyle = [
            'font' => [
                'italic' => true,
                'size' => 11,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Primary blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $dataRowStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $totalRowStyle = [
            'font' => [
                'bold' => true,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'], // Light gray
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // 1. Title Block
        $sheet->setCellValue('A1', 'REKAPITULASI PELANGGARAN POIN SISWA');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Subtitle info
        $className = 'Semua Kelas';
        if ($classId) {
            $kelasObj = Kelas::find($classId);
            $className = $kelasObj ? $kelasObj->class_name : 'Semua Kelas';
        }

        $dateRange = 'Semua Waktu';
        if ($startDate && $endDate) {
            $dateRange = date('d-m-Y', strtotime($startDate)) . ' s/d ' . date('d-m-Y', strtotime($endDate));
        } elseif ($startDate) {
            $dateRange = 'Mulai ' . date('d-m-Y', strtotime($startDate));
        } elseif ($endDate) {
            $dateRange = 'Hingga ' . date('d-m-Y', strtotime($endDate));
        }

        $sheet->setCellValue('A2', "Kelas: {$className} | Periode: {$dateRange}");
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(26);

        // 2. Table Headers
        $headers = [
            'A4' => 'No',
            'B4' => 'Tanggal Kejadian',
            'C4' => 'NISN',
            'D4' => 'Nama Siswa',
            'E4' => 'Kelas',
            'F4' => 'Pelanggaran',
            'G4' => 'Poin',
            'H4' => 'Dicatat Oleh',
            'I4' => 'Keterangan/Deskripsi',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $sheet->getStyle('A4:I4')->applyFromArray($headerStyle);

        // 3. Table Body
        $row = 5;
        $no = 1;
        $totalPoin = 0;

        foreach ($logs as $log) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($log->date_occurred)));
            $sheet->setCellValue('C' . $row, "'" . $log->student->nisn); // quote prefix to force string (NISN starts with 0 often)
            $sheet->setCellValue('D' . $row, $log->student->name);
            $sheet->setCellValue('E' . $row, $log->student->kelas ? $log->student->kelas->class_name : '-');
            $sheet->setCellValue('F' . $row, $log->violation->violation_name);
            $sheet->setCellValue('G' . $row, $log->points_added);
            $sheet->setCellValue('H' . $row, $log->user ? $log->user->name : '-');
            $sheet->setCellValue('I' . $row, $log->description ?: '-');

            // Alignments
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($dataRowStyle);
            $sheet->getRowDimension($row)->setRowHeight(20);

            $totalPoin += $log->points_added;
            $row++;
        }

        // 4. Totals Row
        $sheet->setCellValue('A' . $row, 'TOTAL POIN PELANGGARAN');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, $totalPoin);
        
        // Formats and alignments for totals
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($totalRowStyle);
        $sheet->getRowDimension($row)->setRowHeight(22);

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Write file and stream download
        $fileName = 'Rekap_Pelanggaran_' . str_replace(' ', '_', $className) . '_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Print Student Card (A4 printable page).
     */
    public function printStudentCard(Student $student)
    {
        $user = auth()->user();

        // Security check for Wali Kelas
        if ($user->isWaliKelas()) {
            $myClass = Kelas::where('homeroom_teacher_id', $user->id)->first();
            if (!$myClass || $student->class_id !== $myClass->id) {
                abort(403, 'Akses ditolak. Anda hanya diperbolehkan mengakses data kelas Anda sendiri.');
            }
        }

        // Fetch logs
        $logs = $student->violationLogs()->with(['violation', 'user'])->orderBy('date_occurred', 'desc')->get();

        return view('reports.print_card', compact('student', 'logs'));
    }

    /**
     * Print warning letter (Surat Peringatan).
     */
    public function printStudentSp(Student $student, Request $request)
    {
        $user = auth()->user();

        // Security check for Wali Kelas
        if ($user->isWaliKelas()) {
            $myClass = Kelas::where('homeroom_teacher_id', $user->id)->first();
            if (!$myClass || $student->class_id !== $myClass->id) {
                abort(403, 'Akses ditolak. Anda hanya diperbolehkan mengakses data kelas Anda sendiri.');
            }
        }

        // Determine SP level
        // Options: ?sp=1, ?sp=2, ?sp=3
        $spLevel = $request->query('sp');
        
        if (!in_array($spLevel, [1, 2, 3])) {
            // Auto-detect based on current points
            $points = $student->current_points;
            if ($points >= 100) {
                $spLevel = 3;
            } elseif ($points >= 75) {
                $spLevel = 2;
            } else {
                $spLevel = 1; // Default fallback
            }
        }

        // Fetch violation logs for detail listing in letter if needed
        $logs = $student->violationLogs()->with(['violation', 'user'])->orderBy('date_occurred', 'desc')->get();

        return view('reports.print_sp', compact('student', 'spLevel', 'logs'));
    }
}
