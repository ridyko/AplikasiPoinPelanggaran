<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Major;
use App\Models\User;
use App\Models\Student;
use App\Models\Violation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViolationLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $waliKelas;
    protected Major $major;
    protected Kelas $kelasA;
    protected Kelas $kelasB;
    protected Student $studentA;
    protected Student $studentB;
    protected Violation $violation;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup User (Wali Kelas)
        $this->waliKelas = User::create([
            'name' => 'Wali Kelas Test',
            'email' => 'walikelas@test.com',
            'password' => bcrypt('password'),
            'role' => 'wali_kelas'
        ]);

        // 2. Setup Major
        $this->major = Major::create([
            'code' => 'RPL',
            'name' => 'Rekayasa Perangkat Lunak'
        ]);

        // 3. Setup Class A (Owned by Wali Kelas) and Class B (Not Owned)
        $this->kelasA = Kelas::create([
            'class_name' => 'XII RPL 1',
            'major_id' => $this->major->id,
            'homeroom_teacher_id' => $this->waliKelas->id
        ]);

        $this->kelasB = Kelas::create([
            'class_name' => 'XII RPL 2',
            'major_id' => $this->major->id,
            'homeroom_teacher_id' => null
        ]);

        // 4. Setup Student A (Class A) and Student B (Class B)
        $this->studentA = Student::create([
            'nis' => '111111',
            'name' => 'Student Class A',
            'class_id' => $this->kelasA->id,
            'parent_name' => 'Parent A',
            'parent_phone' => '0811111111',
            'current_points' => 0,
            'status' => 'aktif',
            'tahun_ajaran' => '2025/2026'
        ]);

        $this->studentB = Student::create([
            'nis' => '222222',
            'name' => 'Student Class B',
            'class_id' => $this->kelasB->id,
            'parent_name' => 'Parent B',
            'parent_phone' => '0822222222',
            'current_points' => 0,
            'status' => 'aktif',
            'tahun_ajaran' => '2025/2026'
        ]);

        // 5. Setup a Violation
        $this->violation = Violation::create([
            'violation_name' => 'Terlambat masuk sekolah',
            'category' => 'ringan',
            'points' => 5
        ]);
    }

    public function test_guest_cannot_access_create_violation_page()
    {
        $response = $this->get(route('violations.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_wali_kelas_can_view_students_from_other_classes_in_dropdown()
    {
        $response = $this->actingAs($this->waliKelas)->get(route('violations.create'));
        $response->assertStatus(200);

        $students = $response->viewData('students');
        $this->assertCount(2, $students);
        $this->assertTrue($students->contains($this->studentA));
        $this->assertTrue($students->contains($this->studentB));
    }

    public function test_wali_kelas_can_record_violation_for_student_in_other_class()
    {
        $response = $this->actingAs($this->waliKelas)->post(route('violations.store'), [
            'student_id' => $this->studentB->id,
            'violation_id' => $this->violation->id,
            'date_occurred' => now()->format('Y-m-d'),
            'description' => 'Test description'
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('violation_logs', [
            'student_id' => $this->studentB->id,
            'violation_id' => $this->violation->id,
            'points_added' => $this->violation->points,
            'user_id' => $this->waliKelas->id
        ]);

        // Assert points were incremented
        $this->assertEquals($this->violation->points, $this->studentB->fresh()->current_points);
    }
}
