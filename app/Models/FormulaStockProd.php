<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FormulaStockProd extends Model
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

    public static function getProduct($fcType, $fcPart)
    {
        return self::selectRaw('
                LTRIM(RTRIM(U.FCNAME)) AS UM, 
                PROD.FCSKID, 
                LTRIM(RTRIM(PROD.FCCODE)) AS CPART_NO, 
                LTRIM(RTRIM(PROD.FCSNAME)) AS CCODE, 
                LTRIM(RTRIM(PROD.FCNAME)) AS CPART_NAME, 
                LTRIM(RTRIM(PD.FCCODE)) AS MODEL, 
                LTRIM(RTRIM(PD.FCNAME)) AS SMODEL,
                S.FNQTY AS STOCKQTY
            ')
            ->join('Formula.dbo.vmCorp AS C', 'PROD.FCCORP', '=', 'C.FCSKID')
            ->join('Formula.dbo.PDGRP AS PD', 'PROD.FCPDGRP', '=', 'PD.FCSKID')
            ->join('Formula.dbo.UM AS U', 'PROD.FCUM', '=', 'U.FCSKID')
            ->leftJoin('Formula.dbo.STOCK AS S', 'S.FCPROD', '=', 'PROD.FCSKID')
            ->where('PROD.FCTYPE', $fcType)
            ->where(function ($query) use ($fcPart) {
                $query->where('PROD.FCCODE', 'LIKE', '%' . $fcPart . '%')
                    ->orWhere('PROD.FCNAME', 'LIKE', '%' . $fcPart . '%');
            })
            ->where('S.FCWHOUSE', 'H2u7qN01');
    }

    public static function getSelectedProduct($fcType, $part_selected)
    {
        $part_selected = is_array($part_selected) ? $part_selected : (is_null($part_selected) ? [] : [$part_selected]);

        return self::selectRaw('
                LTRIM(RTRIM(U.FCNAME)) AS UM, 
                PROD.FCSKID, 
                LTRIM(RTRIM(PROD.FCCODE)) AS CPART_NO, 
                LTRIM(RTRIM(PROD.FCSNAME)) AS CCODE, 
                LTRIM(RTRIM(PROD.FCNAME)) AS CPART_NAME, 
                LTRIM(RTRIM(PD.FCCODE)) AS MODEL, 
                LTRIM(RTRIM(PD.FCNAME)) AS SMODEL,
                S.FNQTY AS STOCKQTY
            ')
            ->join('Formula.dbo.vmCorp AS C', 'PROD.FCCORP', '=', 'C.FCSKID')
            ->join('Formula.dbo.PDGRP AS PD', 'PROD.FCPDGRP', '=', 'PD.FCSKID')
            ->join('Formula.dbo.UM AS U', 'PROD.FCUM', '=', 'U.FCSKID')
            ->leftJoin('Formula.dbo.STOCK AS S', 'S.FCPROD', '=', 'PROD.FCSKID')
            ->where('PROD.FCTYPE', $fcType)
            ->where(function ($query) use ($part_selected) {
                foreach ($part_selected as $part) {
                    $query->orWhere('PROD.FCCODE', '=', '%' . $part . '%');
                }
            })
            ->where('S.FCWHOUSE', 'H2u7qN01');
    }

}
