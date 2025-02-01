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
    public function section()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }
    public function class()
    {
        return $this->belongsTo(Section::class, 'sec_id', 'id');
    }
}
