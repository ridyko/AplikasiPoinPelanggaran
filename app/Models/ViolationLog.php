<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationLog extends Model
{
    protected $table = 'violation_logs';

    protected $fillable = [
        'student_id',
        'violation_id',
        'points_added',
        'date_occurred',
        'description',
        'user_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function violation()
    {
        return $this->belongsTo(Violation::class, 'violation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function waQueues()
    {
        return $this->hasMany(WaQueue::class, 'violation_log_id');
    }
}
