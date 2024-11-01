<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobDetail extends Model
{
    protected $fillable = [
        'part_no',
        'part_code',
        'part_name',
        'model',
        'qty',
        'whouse',
        'from_whs',
        'to_whs',
        'status',
        'remark',
        'job_id',
        'created_date',
        'user_id',
    ];

    public function job_head()
    {
        return $this->belongsTo(JobHead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
