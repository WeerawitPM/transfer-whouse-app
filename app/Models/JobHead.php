<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobHead extends Model
{
    protected $fillable = [
        'job_no',
        'doc_no',
        'doc_ref_no',
        'department',
        'from_whs',
        'to_whs',
        'status',
        'remark',
        'created_date',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
