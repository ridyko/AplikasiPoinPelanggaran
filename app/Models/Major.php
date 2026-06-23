<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = ['code', 'name'];

    public function classes()
    {
        return $this->hasMany(Kelas::class, 'major_id');
    }
}
