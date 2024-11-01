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
                LTRIM(RTRIM(TRACK.KANBAN)) AS KANBAN,
                LTRIM(RTRIM(TRACK.CPART_NO)) AS CPART_NO,
                LTRIM(RTRIM(P.FCSNAME)) AS FCSNAME,
                LTRIM(RTRIM(P.FCNAME)) AS FCNAME,
                LTRIM(RTRIM(K.CMODEL)) AS CMODEL,
                LTRIM(RTRIM(K.CPICTURE)) AS CPICTURE
            ')
            ->join('FORMULA.dbo.PROD AS P', function ($join) {
                $join->on(DB::raw('LTRIM(RTRIM(TRACK.CPART_NO))'), '=', DB::raw('LTRIM(RTRIM(P.FCCODE))'));
            })
            ->join('VCST.dbo.KANBAN AS K', function ($join) {
                $join->on(DB::raw('LTRIM(RTRIM(TRACK.CPART_NO))'), '=', DB::raw('LTRIM(RTRIM(K.CPART_NO))'));
            })
            ->where('TRACK.JOB_NO', $job_no)
            ->where('TRACK.CPART_NO', $part_no)
            ->where('TRACK.STEP', 1)
            ->where('TRACK.STATUS', 1);
    }

}
