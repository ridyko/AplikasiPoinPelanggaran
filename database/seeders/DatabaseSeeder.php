<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Major;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\Violation;
use App\Models\ViolationLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ViolationLog::truncate();
        Student::truncate();
        Kelas::truncate();
        Major::truncate();
        Violation::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ============================================================
        // 1. USERS — Super Admin
        // ============================================================
        $superAdmin = User::create([
            'name'     => 'Drs. H. Agus Supriyatna, M.Pd.',
            'email'    => 'superadmin@smkn2jkt.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
        ]);

        // ============================================================
        // 2. USERS — Guru BK (2 orang)
        // ============================================================
        $guruBk1 = User::create([
            'name'     => 'Dra. Sri Wahyuni, M.Pd.',
            'email'    => 'bk1@smkn2jkt.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'guru_bk',
        ]);

        $guruBk2 = User::create([
            'name'     => 'Hendra Kusuma, S.Psi.',
            'email'    => 'bk2@smkn2jkt.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'guru_bk',
        ]);

        // ============================================================
        // 3. USERS — Wakil Kesiswaan (1 orang)
        // ============================================================
        $wakilKesiswaan = User::create([
            'name'     => 'Bambang Irianto, S.Pd., M.M.',
            'email'    => 'wakesis@smkn2jkt.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'wakil_kesiswaan',
        ]);

        // ============================================================
        // 4. USERS — Guru (20 orang, 7 pertama jadi Wali Kelas)
        // ============================================================
        $guruData = [
            // Index 0–6 akan jadi Wali Kelas
            ['name' => 'Rudi Hartono, S.Kom.',          'email' => 'rudi.hartono@smkn2jkt.sch.id'],
            ['name' => 'Yuni Astuti, S.Pd.',             'email' => 'yuni.astuti@smkn2jkt.sch.id'],
            ['name' => 'Fajar Nugroho, S.T.',            'email' => 'fajar.nugroho@smkn2jkt.sch.id'],
            ['name' => 'Rina Marlina, S.Pd.',            'email' => 'rina.marlina@smkn2jkt.sch.id'],
            ['name' => 'Agung Prasetyo, S.Kom.',         'email' => 'agung.prasetyo@smkn2jkt.sch.id'],
            ['name' => 'Lina Susanti, S.E.',             'email' => 'lina.susanti@smkn2jkt.sch.id'],
            ['name' => 'Dedi Setiawan, S.Pd.',           'email' => 'dedi.setiawan@smkn2jkt.sch.id'],
            // Index 7–19 Guru biasa
            ['name' => 'Sari Dewi, S.Pd.',               'email' => 'sari.dewi@smkn2jkt.sch.id'],
            ['name' => 'Haryanto, S.T.',                  'email' => 'haryanto@smkn2jkt.sch.id'],
            ['name' => 'Nurul Hidayah, S.Pd.',           'email' => 'nurul.hidayah@smkn2jkt.sch.id'],
            ['name' => 'Bima Sakti, S.Kom.',             'email' => 'bima.sakti@smkn2jkt.sch.id'],
            ['name' => 'Ratna Sari, S.Pd.',              'email' => 'ratna.sari@smkn2jkt.sch.id'],
            ['name' => 'Wahyu Widodo, S.T.',             'email' => 'wahyu.widodo@smkn2jkt.sch.id'],
            ['name' => 'Mega Puspita, S.Pd.',            'email' => 'mega.puspita@smkn2jkt.sch.id'],
            ['name' => 'Anton Wijaya, S.E.',             'email' => 'anton.wijaya@smkn2jkt.sch.id'],
            ['name' => 'Fitri Rahayu, S.Pd.',            'email' => 'fitri.rahayu@smkn2jkt.sch.id'],
            ['name' => 'Gunawan Saputra, S.T.',          'email' => 'gunawan.saputra@smkn2jkt.sch.id'],
            ['name' => 'Lia Anggraeni, S.Pd.',           'email' => 'lia.anggraeni@smkn2jkt.sch.id'],
            ['name' => 'Tono Budiman, S.Kom.',           'email' => 'tono.budiman@smkn2jkt.sch.id'],
            ['name' => 'Dwi Cahyani, S.Pd.',             'email' => 'dwi.cahyani@smkn2jkt.sch.id'],
        ];

        $guru = [];
        foreach ($guruData as $i => $g) {
            $role = ($i < 7) ? 'wali_kelas' : 'guru';
            $guru[] = User::create([
                'name'     => $g['name'],
                'email'    => $g['email'],
                'password' => Hash::make('password'),
                'role'     => $role,
            ]);
        }

        // ============================================================
        // 5. MAJORS (Jurusan)
        // ============================================================
        $rpl = Major::create(['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak']);
        $dkv = Major::create(['code' => 'DKV', 'name' => 'Desain Komunikasi Visual']);
        $mp  = Major::create(['code' => 'MP',  'name' => 'Manajemen Perkantoran']);
        $ak  = Major::create(['code' => 'AK',  'name' => 'Akuntansi']);
        $br  = Major::create(['code' => 'BR',  'name' => 'Bisnis Ritel']);

        // ============================================================
        // 6. KELAS (7 kelas, masing-masing punya Wali Kelas)
        // ============================================================
        $kelasData = [
            ['class_name' => 'XII RPL 1', 'major' => $rpl, 'wali' => $guru[0]],
            ['class_name' => 'XII RPL 2', 'major' => $rpl, 'wali' => $guru[1]],
            ['class_name' => 'XII DKV 1', 'major' => $dkv, 'wali' => $guru[2]],
            ['class_name' => 'XII DKV 2', 'major' => $dkv, 'wali' => $guru[3]],
            ['class_name' => 'XII MP',    'major' => $mp,  'wali' => $guru[4]],
            ['class_name' => 'XII AK',    'major' => $ak,  'wali' => $guru[5]],
            ['class_name' => 'XII BR',    'major' => $br,  'wali' => $guru[6]],
        ];

        $kelas = [];
        foreach ($kelasData as $k) {
            $kelas[] = Kelas::create([
                'class_name'          => $k['class_name'],
                'major_id'            => $k['major']->id,
                'homeroom_teacher_id' => $k['wali']->id,
            ]);
        }

        // ============================================================
        // 7. VIOLATIONS (Jenis Pelanggaran)
        // ============================================================
        $violations = [];
        $violationList = [
            ['violation_name' => 'Terlambat masuk sekolah',                               'category' => 'ringan', 'points' => 5],
            ['violation_name' => 'Atribut seragam tidak lengkap',                          'category' => 'ringan', 'points' => 5],
            ['violation_name' => 'Pakaian/seragam tidak rapi',                             'category' => 'ringan', 'points' => 5],
            ['violation_name' => 'Rambut tidak rapi/gondrong (laki-laki)',                 'category' => 'ringan', 'points' => 10],
            ['violation_name' => 'Membawa HP ke kelas tanpa izin',                         'category' => 'ringan', 'points' => 10],
            ['violation_name' => 'Keluar lingkungan sekolah tanpa izin',                   'category' => 'sedang', 'points' => 15],
            ['violation_name' => 'Bolos/sengaja tidak mengikuti pelajaran',                'category' => 'sedang', 'points' => 15],
            ['violation_name' => 'Membawa/merokok di lingkungan sekolah',                  'category' => 'sedang', 'points' => 25],
            ['violation_name' => 'Merusak sarana dan prasarana sekolah',                   'category' => 'sedang', 'points' => 30],
            ['violation_name' => 'Terlibat pertengkaran/perkelahian/tawuran',              'category' => 'berat',  'points' => 75],
            ['violation_name' => 'Melakukan tindakan kriminal, asusila, atau pencurian',   'category' => 'berat',  'points' => 100],
            ['violation_name' => 'Membawa atau menggunakan narkoba/minuman keras',          'category' => 'berat',  'points' => 100],
        ];

        foreach ($violationList as $v) {
            $violations[] = Violation::create($v);
        }

        // ============================================================
        // 8. STUDENTS (70 siswa, 10 per kelas)
        // ============================================================
        $namaDepanLaki  = ['Aditya','Bima','Fajar','Reza','Dani','Eko','Gilang','Hendra','Ilham','Joko','Kevin','Lukman','Muhamad','Nanda','Oki','Pandu','Rizky','Surya','Taufik','Umar'];
        $namaDepanPerem = ['Ayu','Bella','Citra','Desi','Evi','Fitri','Gita','Hani','Indah','Julia','Kartika','Lina','Maya','Nisa','Okta','Putri','Rini','Sari','Tia','Ulfa'];
        $namaBelakang   = ['Pratama','Santoso','Hidayat','Nugroho','Setiawan','Wijaya','Kusuma','Saputra','Purnama','Rahayu','Dewi','Lestari','Anggraeni','Cahyani','Utami','Andriani','Susanti','Wulandari','Permata','Sanjaya'];
        $namaOrangtua   = ['Hartono','Suharto','Bambang','Supriyadi','Wahyudi','Sudirman','Mulyono','Haryadi','Joko Widodo','Agus Salim','Siti Rahayu','Endang','Mulyati','Sri Wahyuni','Retno','Slamet','Poniman','Margono','Triyono','Darmin'];
        $prefixNIS = 990000;

        $siswaIndex = 0;
        $allStudents = [];

        foreach ($kelas as $ki => $k) {
            for ($s = 0; $s < 10; $s++) {
                $isLaki      = ($s % 2 === 0);
                $namaDepan   = $isLaki
                    ? $namaDepanLaki[($ki * 5 + $s) % count($namaDepanLaki)]
                    : $namaDepanPerem[($ki * 5 + $s) % count($namaDepanPerem)];
                $namaBelak   = $namaBelakang[($siswaIndex) % count($namaBelakang)];
                $namaOrtu    = $namaOrangtua[($siswaIndex) % count($namaOrangtua)];
                $nis        = sprintf('%06d', $prefixNIS + $siswaIndex + 1);
                $phoneOrtu   = '08' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);

                $student = Student::create([
                    'nis'            => $nis,
                    'name'           => $namaDepan . ' ' . $namaBelak,
                    'class_id'       => $k->id,
                    'parent_name'    => $namaOrtu,
                    'parent_phone'   => $phoneOrtu,
                    'current_points' => 0,
                    'status'         => 'aktif',
                    'tahun_ajaran'   => '2025/2026',
                ]);

                $allStudents[] = $student;
                $siswaIndex++;
            }
        }

        // ============================================================
        // 9. VIOLATION LOGS — Data realistis
        //    ~40% siswa punya pelanggaran bervariasi
        // ============================================================
        $recorders   = [$guruBk1, $guruBk2, $wakilKesiswaan, $guru[7], $guru[8], $guru[9]];
        $ringanIds   = [0, 1, 2, 3, 4]; // index ke $violations
        $sedangIds   = [5, 6, 7, 8];
        $beratIds    = [9, 10, 11];

        // Pilih 28 siswa secara acak untuk mendapat pelanggaran
        $chosen = array_rand($allStudents, 28);
        sort($chosen);

        $baseDate = Carbon::now()->subDays(90);

        foreach ($chosen as $ci => $idx) {
            $student  = $allStudents[$idx];
            $recorder = $recorders[array_rand($recorders)];

            // Tentukan jumlah pelanggaran (1–4)
            $jumlah = rand(1, 4);
            // 70% pelanggaran ringan, 20% sedang, 10% berat
            $pool = array_merge(
                array_fill(0, 7, 'ringan'),
                array_fill(0, 2, 'sedang'),
                array_fill(0, 1, 'berat')
            );

            $totalPoin = 0;
            for ($p = 0; $p < $jumlah; $p++) {
                $tipe = $pool[array_rand($pool)];

                switch ($tipe) {
                    case 'ringan':
                        $vioIdx = $violations[$ringanIds[array_rand($ringanIds)]];
                        break;
                    case 'sedang':
                        $vioIdx = $violations[$sedangIds[array_rand($sedangIds)]];
                        break;
                    default:
                        $vioIdx = $violations[$beratIds[array_rand($beratIds)]];
                        break;
                }

                $dayOffset = rand(0, 80);
                $tanggal   = $baseDate->copy()->addDays($dayOffset)->format('Y-m-d');

                ViolationLog::create([
                    'student_id'   => $student->id,
                    'violation_id' => $vioIdx->id,
                    'points_added' => $vioIdx->points,
                    'date_occurred'=> $tanggal,
                    'description'  => null,
                    'user_id'      => $recorder->id,
                ]);

                $totalPoin += $vioIdx->points;
            }

            // Update poin siswa
            $student->update(['current_points' => $totalPoin]);

            // Jika poin kritis (>= 75), set status skorsing
            if ($totalPoin >= 100) {
                $student->update(['status' => 'skorsing']);
            }
        }
    }
}
