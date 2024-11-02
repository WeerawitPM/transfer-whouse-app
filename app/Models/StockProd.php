<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockProd extends Model
{
    // ระบุการเชื่อมต่อฐานข้อมูล
    protected $connection = 'formula';

    // ชื่อของตารางที่ต้องการเชื่อมโยง
    protected $table = 'PROD';

    // ระบุฟิลด์ที่เป็น primary key
    protected $primaryKey = 'FCSKID';
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
}
