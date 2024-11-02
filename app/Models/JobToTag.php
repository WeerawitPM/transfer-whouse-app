<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobToTag extends Model
{
    protected $fillable = [
        'image',
        'kanban',
        'part_no',
        'part_code',
        'part_name',
        'model',
        'qty',
        'packing_name',
        'whouse',
        'from_whs',
        'to_whs',
        'qr_code',
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
