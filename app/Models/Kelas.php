<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'classes';

    protected $fillable = ['class_name', 'major_id', 'homeroom_teacher_id'];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
