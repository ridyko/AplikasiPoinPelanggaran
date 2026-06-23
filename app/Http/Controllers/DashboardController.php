<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ViolationLog;
use App\Models\Kelas;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected WhatsappService $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $user = auth()->user();
        $isWaliKelas = $user->isWaliKelas();
        $myClass = null;

        if ($isWaliKelas) {
            $myClass = Kelas::where('homeroom_teacher_id', $user->id)->first();
        }

        // Class student list for Wali Kelas
        $classStudents = collect();

        // Aggregate statistics
        if ($isWaliKelas) {
            if ($myClass) {
                $totalStudents = $myClass->students()->count();
                $criticalStudents = $myClass->students()->where('current_points', '>=', 50)->count();
                $todayCases = ViolationLog::whereIn('student_id', $myClass->students()->pluck('id'))
                    ->whereDate('date_occurred', today())
                    ->count();
                $recentLogs = ViolationLog::whereIn('student_id', $myClass->students()->pluck('id'))
                    ->with(['student.kelas', 'violation', 'user', 'waQueues'])
                    ->latest()
                    ->limit(5)
                    ->get();
                // Full sorted student list for the class panel
                $classStudents = Student::where('class_id', $myClass->id)
                    ->orderBy('current_points', 'desc')
                    ->orderBy('name')
                    ->get();
            } else {
                $totalStudents = 0;
                $criticalStudents = 0;
                $todayCases = 0;
                $recentLogs = collect();
            }
        } else {
            // Guru BK / Admin (Full access)
            $totalStudents = Student::count();
            $criticalStudents = Student::where('current_points', '>=', 50)->count();
            $todayCases = ViolationLog::whereDate('date_occurred', today())->count();
            $recentLogs = ViolationLog::with(['student.kelas', 'violation', 'user', 'waQueues'])
                ->latest()
                ->limit(5)
                ->get();
        }

        // Chart Data 1: Category distribution (Ringan, Sedang, Berat)
        $categoryQuery = ViolationLog::join('violations', 'violation_logs.violation_id', '=', 'violations.id')
            ->select('violations.category', DB::raw('count(*) as count'));
        if ($isWaliKelas && $myClass) {
            $categoryQuery->whereIn('violation_logs.student_id', $myClass->students()->pluck('id'));
        }
        $categoryStats = $categoryQuery->groupBy('violations.category')->get()->pluck('count', 'category')->toArray();
        $chartCategories = [
            'ringan' => $categoryStats['ringan'] ?? 0,
            'sedang' => $categoryStats['sedang'] ?? 0,
            'berat' => $categoryStats['berat'] ?? 0,
        ];

        // Chart Data 2: Top 5 Violations
        $topViolationsQuery = ViolationLog::join('violations', 'violation_logs.violation_id', '=', 'violations.id')
            ->select('violations.violation_name', DB::raw('count(*) as count'));
        if ($isWaliKelas && $myClass) {
            $topViolationsQuery->whereIn('violation_logs.student_id', $myClass->students()->pluck('id'));
        }
        $topViolations = $topViolationsQuery->groupBy('violations.violation_name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Chart Data 3: Monthly Trend (Last 6 months)
        $monthlyTrendQuery = ViolationLog::select(
            DB::raw("DATE_FORMAT(date_occurred, '%Y-%m') as month_year"),
            DB::raw('count(*) as count')
        );
        if ($isWaliKelas && $myClass) {
            $monthlyTrendQuery->whereIn('violation_logs.student_id', $myClass->students()->pluck('id'));
        }
        $monthlyData = $monthlyTrendQuery->where('date_occurred', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month_year')
            ->orderBy('month_year', 'asc')
            ->get()
            ->pluck('count', 'month_year')
            ->toArray();

        $chartMonths = [];
        $chartMonthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $monthLabel = now()->subMonths($i)->format('M Y');
            $chartMonths[] = $monthLabel;
            $chartMonthlyCounts[] = $monthlyData[$monthKey] ?? 0;
        }

        // Chart Data 4: Top 5 Undisciplined Classes (BK/Admin only)
        $topClasses = [];
        if (!$isWaliKelas) {
            $topClasses = Student::join('classes', 'students.class_id', '=', 'classes.id')
                ->select('classes.class_name', DB::raw('sum(students.current_points) as total_points'))
                ->groupBy('classes.class_name')
                ->orderBy('total_points', 'desc')
                ->limit(5)
                ->get();
        }

        // WhatsApp Gateway Status
        $waStatus = $this->whatsappService->getStatus();

        return view('dashboard', compact(
            'totalStudents',
            'criticalStudents',
            'todayCases',
            'recentLogs',
            'isWaliKelas',
            'myClass',
            'classStudents',
            'waStatus',
            'chartCategories',
            'topViolations',
            'chartMonths',
            'chartMonthlyCounts',
            'topClasses'
        ));
    }

    public function startGateway()
    {
        // Check if already running
        $waStatus = $this->whatsappService->getStatus();
        if ($waStatus['status'] !== 'offline') {
            return back()->with('success', 'WhatsApp Gateway sudah aktif dan berjalan.');
        }

        $path = base_path('whatsapp-gateway');
        $command = "export PATH=\$PATH:/usr/local/bin && cd {$path} && (for fd in \$(ls /dev/fd/); do [ \"\$fd\" -gt 2 ] 2>/dev/null && eval \"exec \$fd>&-\"; done; nohup /usr/local/bin/node index.js < /dev/null > /dev/null 2>&1 &)";
        pclose(popen($command, 'r'));

        // Wait a brief moment for the service to bind to port 3000
        sleep(2);

        return back()->with('success', 'WhatsApp Gateway berhasil dinyalakan! QR Code akan muncul di bawah ini dalam beberapa detik.');
    }

    public function logoutGateway()
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses.');
        }

        $result = $this->whatsappService->logout();

        if ($result['success']) {
            return back()->with('success', 'Sesi WhatsApp berhasil didelete. Silakan scan kembali menggunakan nomor yang baru.');
        }

        return back()->with('error', $result['error'] ?? 'Gagal memutuskan koneksi.');
    }

    public function stopGateway()
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses.');
        }

        // Find the process listening on port 3000 and kill it
        $pid = shell_exec("lsof -t -i:3000");
        if ($pid) {
            $pid = trim($pid);
            exec("kill -9 {$pid}");
            return back()->with('success', 'WhatsApp Gateway berhasil dimatikan (Status: Offline).');
        }

        return back()->with('error', 'WhatsApp Gateway tidak sedang aktif.');
    }
}
