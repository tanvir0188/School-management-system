<?php

namespace App\Models;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    //
    protected $fillable = [
        'name',
    ];
    public function exam()
    {
        return $this->hasMany(Exam::class, 'exam_type_id', 'id');
    }
}
