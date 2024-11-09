<?php

namespace App\Filament\Resources\TransferBookMenuResource\Functions;

use App\Models\JobDetail;
use App\Models\JobHead;
use App\Models\JobToTag;

class handleJob
{
    public static function handleUpdateJobHead($job_id)
    {
        JobHead::where('id', $job_id)->update(['status' => 1]);
        // dd($job_id, JobHead::where('id', $job_id)->first()->get());
        return;
    }

    public static function handleUpdateJobToTag($jobToTag)
    {
        foreach ($jobToTag as $tag) {
            JobToTag::where('id', $tag['id'])->update(['status' => 1]);
        }
        return;
    }

    public static function handleUpdateJobDetail($jobDetail)
    {
        foreach ($jobDetail as $tag) {
            JobDetail::where('id', $tag['id'])->update(['status' => 1]);
        }
        return;
    }
}