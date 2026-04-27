<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'position'
    ];

    // relasi ke attendance
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}