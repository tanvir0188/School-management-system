<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    //

    protected $fillable = [
        'full_name',
        'student_id',
        'photo',
        'father_name',
        'mother_name',
        'age',
        'phone_number',
        'address',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
