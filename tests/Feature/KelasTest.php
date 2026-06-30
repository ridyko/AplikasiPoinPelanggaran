<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Major;
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelasTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $guruBk;
    protected Major $major;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin'
        ]);

        $this->guruBk = User::create([
            'name' => 'Guru BK',
            'email' => 'gurubk@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru_bk'
        ]);

        $this->major = Major::create([
            'code' => 'RPL',
            'name' => 'Rekayasa Perangkat Lunak'
        ]);
    }

    public function test_guest_cannot_access_kelas_page()
    {
        $response = $this->get(route('kelas.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_super_admin_cannot_access_kelas_page()
    {
        $response = $this->actingAs($this->guruBk)->get(route('kelas.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_kelas_page()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('kelas.index'));
        $response->assertStatus(200);
        $response->assertViewIs('kelas.index');
        $response->assertViewHasAll(['kelas', 'majors', 'waliKandidates']);
    }

    public function test_super_admin_can_store_new_class()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('kelas.store'), [
            'class_name' => 'XII RPL 1',
            'major_id' => $this->major->id,
            'homeroom_teacher_id' => null
        ]);

        $response->assertRedirect(route('kelas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('classes', [
            'class_name' => 'XII RPL 1',
            'major_id' => $this->major->id,
        ]);
    }

    public function test_super_admin_cannot_store_duplicate_class_name()
    {
        Kelas::create([
            'class_name' => 'XII RPL 1',
            'major_id' => $this->major->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('kelas.store'), [
            'class_name' => 'XII RPL 1',
            'major_id' => $this->major->id,
        ]);

        $response->assertSessionHasErrors('class_name');
    }

    public function test_super_admin_can_update_class()
    {
        $kelas = Kelas::create([
            'class_name' => 'XII RPL 1',
            'major_id' => $this->major->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('kelas.update', $kelas), [
            'class_name' => 'XII RPL 2',
            'major_id' => $this->major->id,
            'homeroom_teacher_id' => null
        ]);

        $response->assertRedirect(route('kelas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('classes', [
            'id' => $kelas->id,
            'class_name' => 'XII RPL 2',
        ]);
    }

    public function test_super_admin_can_delete_class_without_students()
    {
        $kelas = Kelas::create([
            'class_name' => 'XII RPL 1',
            'major_id' => $this->major->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('kelas.destroy', $kelas));

        $response->assertRedirect(route('kelas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('classes', [
            'id' => $kelas->id,
        ]);
    }

    public function test_super_admin_cannot_delete_class_with_students()
    {
        $kelas = Kelas::create([
            'class_name' => 'XII RPL 1',
            'major_id' => $this->major->id,
        ]);

        Student::create([
            'nis' => '123456',
            'name' => 'Test Student',
            'class_id' => $kelas->id,
            'parent_name' => 'Parent',
            'parent_phone' => '08123456789',
            'current_points' => 0,
            'status' => 'aktif',
            'tahun_ajaran' => '2025/2026'
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('kelas.destroy', $kelas));

        $response->assertRedirect(route('kelas.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('classes', [
            'id' => $kelas->id,
        ]);
    }
}
