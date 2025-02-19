<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    //
    protected $fillable = [
        'full_name',
        'teacher_id',
        'photo',
        'father_name',
        'mother_name',
        'age',
        'phone_number',
        'address',
        'description',
        'position',
    ];
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
