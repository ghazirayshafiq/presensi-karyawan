<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    protected $table = 'attendence'; 

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}