<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionNotice extends Model
{
    //
    protected $table = 'section_notices';
    protected $fillable = [
        'sec_id',
        'title',
        'content',
    ];
    public function section()
    {
        return $this->belongsTo(Section::class, 'sec_id');
    }
}
