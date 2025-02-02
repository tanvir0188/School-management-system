<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    //
    protected $fillable = [
        'exam_type_id',
        'subject',
        'class_id',
        'exam_date',
    ];

    public function exam_type()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    public function exam_result()
    {
        return $this->hasMany(ExamResult::class, 'exam_id', 'id');
    }
}
