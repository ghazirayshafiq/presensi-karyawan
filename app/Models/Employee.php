<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name','position'];

    public function attendences()
    {
        return $this->hasMany(Attendence::class);
    }
}