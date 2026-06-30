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
        // 2. MAJORS (Jurusan)
        // ============================================================
        Major::create(['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak']);
        Major::create(['code' => 'DKV', 'name' => 'Desain Komunikasi Visual']);
        Major::create(['code' => 'MP',  'name' => 'Manajemen Perkantoran']);
        Major::create(['code' => 'AK',  'name' => 'Akuntansi']);
        Major::create(['code' => 'BR',  'name' => 'Bisnis Ritel']);

        // ============================================================
        // 3. VIOLATIONS (Jenis Pelanggaran)
        // ============================================================
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
            Violation::create($v);
        }
    }
}
