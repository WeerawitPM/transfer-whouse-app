<?php

use App\Models\JobDetail;
use App\Models\JobHead;
use App\Models\JobToTag;

function handleUpdateJobHead($job_id)
{
    JobHead::where('id', $job_id)->update(['status' => 1]);
    // dd($job_id, JobHead::where('id', $job_id)->first()->get());
}

function handleUpdateJobToTag($jobToTag)
{
    foreach ($jobToTag as $tag) {
        JobToTag::where('id', $tag['id'])->update(['status' => 1]);
    }
}

function handleUpdateJobDetail($jobDetail)
{
    foreach ($jobDetail as $tag) {
        JobDetail::where('id', $tag['id'])->update(['status' => 1]);
    }
}