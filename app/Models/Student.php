<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Student extends Model
{
    //
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'student_id',
        'password',
    ];

    public function profile()
    {
        return $this->hasOne(StudentProfile::class);
    }
}
