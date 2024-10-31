<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VcstTrackDetail extends Model
{
    // ระบุการเชื่อมต่อฐานข้อมูล
    protected $connection = 'kanban';

    // ชื่อของตารางที่ต้องการเชื่อมโยง
    protected $table = 'TRACK';

    // ระบุฟิลด์ที่เป็น primary key
    protected $primaryKey = 'KANBAN';
    protected $keyType = 'string';

    // ถ้าตารางนี้ไม่มีฟิลด์ timestamps
    public $timestamps = false;

    protected $fillable = [
    ];

    public static function getTrackDetail($job_no, $part_no)
    {
        return self::selectRaw('
                LTRIM(RTRIM(KANBAN)) AS KANBAN,
                LTRIM(RTRIM(CPART_NO)) AS PART_NO
            ')
            ->where('JOB_NO', $job_no)
            ->where('CPART_NO', $part_no)
            ->where('STEP', 1)
            ->where('STATUS', 1);
    }
}
