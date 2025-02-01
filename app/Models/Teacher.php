<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Model
{
    //
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',

    ];
    public function section()
    {
        return $this->hasMany(Section::class, 'teacher_id', 'id'); //id of teachers table will be matched against teacher_id of section table
    }
}
