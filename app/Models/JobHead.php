<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'job_master',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         // Get current year and month
    //         $yearMonth = Carbon::now()->format('Ym');

    //         // Get the last run number for the current month
    //         $lastJob = static::where('job_no', 'like', "JB$yearMonth/%")->orderBy('id', 'desc')->first();
    //         $lastRunNumber = $lastJob ? intval(explode('/', $lastJob->job_no)[1]) : 0;

    //         // Increment run number
    //         $newRunNumber = $lastRunNumber + 1;

    //         // Generate new job_no
    //         $model->job_no = sprintf('JB%s/%d', $yearMonth, $newRunNumber);
    //     });
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobToTags()
    {
        return $this->hasMany(JobToTag::class, 'job_id');
    }

    public function jobDetails()
    {
        return $this->hasMany(JobDetail::class, 'job_id');
    }
}
