<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VcstTrack extends Model
{
    // ระบุการเชื่อมต่อฐานข้อมูล
    protected $connection = 'vcst';

    // ชื่อของตารางที่ต้องการเชื่อมโยง
    protected $table = 'TRACK';

    // ระบุฟิลด์ที่เป็น primary key
    protected $primaryKey = 'JOB_NO';
    protected $keyType = 'string';

    // ถ้าตารางนี้ไม่มีฟิลด์ timestamps
    public $timestamps = false;

    protected $fillable = [
    ];

    // public static function getFilteredData1($start_date, $end_date)
    // {
    //     return DB::connection('kanban')->table('TRACK AS T')
    //         ->selectRaw('
    //         LTRIM(RTRIM(T.JOB_NO)) AS JOB_NO,
    //         LTRIM(RTRIM(T.CPART_NO)) AS CPART_NO,
    //         LTRIM(RTRIM(P.FCSNAME)) AS FCSNAME,
    //         LTRIM(RTRIM(P.FCNAME)) AS FCNAME,
    //         MAX(T.STARTDATE) AS STARTDATE,
    //         MAX(T.ENDDATE) AS ENDDATE
    //     ')
    //         ->join('FORMULA.dbo.PROD AS P', function ($join) {
    //             $join->on(DB::raw('LTRIM(RTRIM(T.CPART_NO))'), '=', DB::raw('LTRIM(RTRIM(P.FCCODE))'));
    //         })
    //         ->where('T.STEP', 1)
    //         ->where('T.STATUS', 1)
    //         ->whereNotNull('T.ENDDATE')
    //         ->whereBetween(DB::raw('CONVERT(date, T.ENDDATE, 103)'), [$start_date, $end_date])
    //         ->groupBy('T.JOB_NO', 'T.CPART_NO', 'P.FCSNAME', 'P.FCNAME')
    //         ->limit(100);
    // }

    public static function getTrack($start_date, $end_date)
    {
        return self::selectRaw('
                LTRIM(RTRIM(JOB_NO)) AS JOB_NO,
                LTRIM(RTRIM(CPART_NO)) AS CPART_NO,
                LTRIM(RTRIM(P.FCSNAME)) AS FCSNAME,
                LTRIM(RTRIM(P.FCNAME)) AS FCNAME,
                MAX(STARTDATE) AS STARTDATE,
                MAX(ENDDATE) AS ENDDATE
            ')
            ->join('FORMULA.dbo.PROD AS P', function ($join) {
                $join->on(DB::raw('LTRIM(RTRIM(CPART_NO))'), '=', DB::raw('LTRIM(RTRIM(P.FCCODE))'));
            })
            ->where('STEP', 1)
            ->where('STATUS', 1)
            ->whereNotNull('ENDDATE')
            // ->where(DB::raw('CONVERT(date, STARTDATE, 103)'), '=', $start_date)
            ->whereBetween(DB::raw('CONVERT(date, ENDDATE, 103)'), [$start_date, $end_date])
            ->groupBy('JOB_NO', 'CPART_NO', 'P.FCSNAME', 'P.FCNAME');
    }
}