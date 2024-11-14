<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fccode_Glref extends Model
{
    protected $connection = 'formula';
    protected $table = 'GLREF';
    protected $primaryKey = 'FCSKID';
    protected $keyType = 'string';

    /**
     * Get the next FCCODE for a given year and month.
     * 
     * @param string $bookFcsKid
     * @param int $year
     * @param int $month
     * @return int
     */
    public static function get_fccode_glref($bookFcsKid, $year, $month)
    {
        // Initialize the variables
        $num = 0;
        $num1 = '0';

        // Prepare the query to get the current FCCODE
        $result = DB::connection('formula')->selectOne(
            'DECLARE @NUM INT = 0;
            DECLARE @NUM1 NVARCHAR(100) = \'0\';

            SELECT TOP 1 @NUM = (FCCODE + 1)
            FROM [formula].[dbo].[GLREF] 
            WHERE FCCORP = \'H2ZFEv02\' 
            AND FCBOOK = LTRIM(RTRIM(?))  
            AND YEAR(FDDATE) = ?
            AND MONTH(FDDATE) = ?
            AND LEN(FCCODE) = 9
            ORDER BY RIGHT(LTRIM(RTRIM(REPLACE(REPLACE(REPLACE([FCCODE], CHAR(32), \'()\'), \')(\', \'\'), \'()\', CHAR(32)))), 9) DESC;

            IF @NUM = 0
            BEGIN
                SET @NUM1 = CONCAT(\'67\', ?, \'00000\');
                SET @NUM = CAST(@NUM1 AS INT);
            END

            SELECT @NUM + 1 AS FCCODE',
            [
                $bookFcsKid, // First parameter for FCBOOK
                $year,       // Second parameter for YEAR(FDDATE)
                $month,      // Third parameter for MONTH(FDDATE)
                $month       // Fourth parameter for CONCAT('67', month, '00000')
            ]
        );

        // Return the FCCODE or set it to 0 if no result
        return $result ? (int) $result->FCCODE : 0;
    }

    public static function get_frcode_glref_store($bookFcsKid, $year, $month)
    {
        $result = DB::connection('itc_wms')->selectOne(
            'EXEC dbo.GET_FCCODE_GLREF ?, ?, ?',
            [$bookFcsKid, $year, $month]
        );
        return $result ? (int) $result->FCCODE : 0;
    }

    public static function get_glref($book_fcskid, $start_date, $end_date)
    {
        return self::selectRaw('
                GLREF.FCSKID as FCSKID,
                LTRIM(RTRIM(GLREF.FCCODE)) as DOC_NO,
                LTRIM(RTRIM(GLREF.FCREFNO)) as REF_NO,
                GLREF.FDDATE,
                LTRIM(RTRIM(wf.FCCODE)) as FROM_WHS,
                LTRIM(RTRIM(wt.FCCODE)) as TO_WHS,
                LTRIM(RTRIM(s.FCNAME)) as SECT
        ')
            ->join('FORMULA.dbo.WHOUSE as wf', 'wf.FCSKID', '=', 'GLREF.FCFRWHOUSE')
            ->join('FORMULA.dbo.WHOUSE as wt', 'wt.FCSKID', '=', 'GLREF.FCTOWHOUSE')
            ->join('SECT as s', function ($join) {
                $join->on('s.FCSKID', '=', 'GLREF.FCSECT')
                    ->on('s.FCDEPT', '=', 'GLREF.FCDEPT');
            })
            ->where('GLREF.FCBOOK', $book_fcskid)
            ->whereBetween(DB::raw("CONVERT(date, GLREF.FDDATE, 103)"), [$start_date, $end_date])
            ->where('GLREF.FCSTAT', '<>', 'C');
    }

    public static function get_glref_prod($glref_fcskid)
    {
        return self::selectRaw('
            GLREF.FCSKID AS FCSKID,
            LTRIM(RTRIM(GLREF.FCCODE)) AS DOC_NO,
            LTRIM(RTRIM(GLREF.FCREFNO)) AS REF_NO,
            GLREF.FDDATE,LTRIM(RTRIM(WF.FCCODE)) AS FROM_WHS,
            LTRIM(RTRIM(WT.FCCODE)) AS TO_WHS,
            LTRIM(RTRIM(S.FCNAME)) AS DEPARTMENT,
            LTRIM(RTRIM(P.FCCODE)) AS PART_NO,
            LTRIM(RTRIM(P.FCSNAME)) AS PART_CODE,
            LTRIM(RTRIM(P.FCNAME)) AS PART_NAME, 
            LTRIM(RTRIM(PD.FCCODE)) AS MODEL, 
            LTRIM(RTRIM(R.FCPRODTYPE)) AS FCPROTYPE, 
            LTRIM(RTRIM(R.FCREFPDTYP)) AS FCREFPDTYP,
            R.FNQTY AS QTY,
            LTRIM(RTRIM(U.FCNAME)) AS UNIT
        ')
            ->join('FORMULA.dbo.WHOUSE as WF', 'WF.FCSKID', '=', 'GLREF.FCFRWHOUSE')
            ->join('FORMULA.dbo.WHOUSE as WT', 'WT.FCSKID', '=', 'GLREF.FCTOWHOUSE')
            ->join('SECT as S', function ($join) {
                $join->on('S.FCSKID', '=', 'GLREF.FCSECT')
                    ->on('S.FCDEPT', '=', 'GLREF.FCDEPT');
            })
            ->join('FORMULA.dbo.REFPROD as R', 'R.FCGLREF', '=', 'GLREF.FCSKID')
            ->join('FORMULA.dbo.PROD as P', 'P.FCSKID', '=', 'R.FCPROD')
            ->join('FORMULA.dbo.PDGRP as PD', 'P.FCPDGRP', '=', 'PD.FCSKID')
            ->join('FORMULA.dbo.UM as U', 'U.FCSKID', '=', 'R.FCUM')
            ->where('GLREF.FCSKID', $glref_fcskid)
            ->where('R.FCIOTYPE', 'I')
            ->where('R.FCSTAT', '<>', 'C');
            // ->where('P.FCTYPE', '1');
    }
}
