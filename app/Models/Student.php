<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nisn',
        'name',
        'class_id',
        'parent_name',
        'parent_phone',
        'current_points',
        'status',
        'tahun_ajaran'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function violationLogs()
    {
        return $this->hasMany(ViolationLog::class, 'student_id');
    }
}
