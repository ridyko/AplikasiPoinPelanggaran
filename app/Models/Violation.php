<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $fillable = ['violation_name', 'category', 'points'];

    public function violationLogs()
    {
        return $this->hasMany(ViolationLog::class, 'violation_id');
    }
}
