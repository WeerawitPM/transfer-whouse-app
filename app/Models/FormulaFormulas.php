<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FormulaFormulas extends Model
{
    // ระบุการเชื่อมต่อฐานข้อมูล
    protected $connection = 'formula';

    // ชื่อของตารางที่ต้องการเชื่อมโยง
    protected $table = 'FORMULAS';

    // ระบุฟิลด์ที่เป็น primary key
    protected $primaryKey = 'FCSKID';
    protected $keyType = 'string';

    // ถ้าตารางนี้ไม่มีฟิลด์ timestamps
    public $timestamps = false;

    protected $fillable = [
    ];

    public static function getChildProduct($part_no)
    {
        return self::selectRaw('
            LTRIM(RTRIM(FORMULAS.FCUM)) AS UM_MOM, 
            LTRIM(RTRIM(FORMULAS.FCSKID)) AS CKEY, 
            LTRIM(RTRIM(FORMULAS.FCCODE)) AS CODE_MOM, 
            LTRIM(RTRIM(FORMULAS.FCNAME)) AS NAME_MOM, 
            LTRIM(RTRIM(P.FCCODE)) AS CODE_SON, 
            LTRIM(RTRIM(P.FCNAME)) AS NAME_SON,
            LTRIM(RTRIM(P.FCSNAME)) AS CCODE, 
            LTRIM(RTRIM(P.FCSKID)) AS KEY_SON, 
            S.FNQTY AS NQTY,
            LTRIM(RTRIM(P.FCUM)) AS UM_SON,
            P.FNSTDCOST AS FNSTDCOST
        ')
            ->join('FORMULA.dbo.vmCorp AS C', function ($join) {
                $join->on(DB::raw('FORMULAS.FCCORP'), '=', DB::raw('C.FCSKID'));
            })
            ->join('FORMULA.dbo.PDSTRUCT AS S', function ($join) {
                $join->on(DB::raw('FORMULAS.FCSKID'), '=', DB::raw('S.FCFORMULAS'));
            })
            ->join('FORMULA.dbo.PROD AS P', function ($join) {
                $join->on(DB::raw('S.FCCOMPO'), '=', DB::raw('P.FCSKID'));
            })
            ->where('FORMULAS.FCCODE', $part_no);
    }
}
