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
        'class_id',
        'sec_id',

    ];

    public function profile()
    {
        return $this->hasOne(StudentProfile::class);
    }
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'sec_id', 'id');
    }
    public function exam_results()
    {
        return $this->hasMany(ExamResult::class, 'student_id', 'id');
    }
}
