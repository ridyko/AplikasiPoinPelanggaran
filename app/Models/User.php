<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Semua role yang tersedia */
    const ROLES = [
        'super_admin'      => 'Super Admin',
        'guru_bk'          => 'Guru BK',
        'wakil_kesiswaan'  => 'Wakil Kesiswaan',
        'wali_kelas'       => 'Wali Kelas',
        'guru'             => 'Guru',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function classes()
    {
        return $this->hasMany(Kelas::class, 'homeroom_teacher_id');
    }

    public function violationLogs()
    {
        return $this->hasMany(ViolationLog::class, 'user_id');
    }

    // ─── Role Helper Methods ─────────────────────────────────────

    /** Hanya Super Admin */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /** Super Admin atau yang setara BK (wakil_kesiswaan) */
    public function isBkOrAbove(): bool
    {
        return in_array($this->role, ['super_admin', 'guru_bk', 'wakil_kesiswaan']);
    }

    /** Role yang bisa Catat Pelanggaran (semua kecuali publik) */
    public function canRecordViolation(): bool
    {
        return in_array($this->role, ['super_admin', 'guru_bk', 'wakil_kesiswaan', 'wali_kelas', 'guru']);
    }

    /** Apakah Wali Kelas */
    public function isWaliKelas(): bool
    {
        return $this->role === 'wali_kelas';
    }

    /** Label role yang ramah pengguna */
    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
