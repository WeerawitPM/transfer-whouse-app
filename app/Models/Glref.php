<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Glref extends Model
{
    protected $connection = 'itc_wms';
    protected $table = 'GLREF';

    public static function insertGlrefData($data)
    {
        $FCDATASER = '$$$+';
        $FCLUPDAPP = "$0";
        $FCCORP = "H2ZFEv02";
        $FCBRANCH = "H2Z2kf01";
        $FCSTEP = "I";
        $FCJOB = "H2ZFfr02";
        $FCCORRECTB = "";
        $FCATSTEP = "X";
        $FCCREATEAP = "";
        $FCPROJ = "x/•ู((()";
        $FCEAFTERR = "E";
        $FTDATETIME = now(); // Or use Carbon::now() if using Carbon

        // Perform the insert
        return DB::connection('itc_wms')->insert(
            'INSERT INTO GLREF (
                FCDATASER, FCSKID, FCLUPDAPP, FCRFTYPE, FCREFTYPE, FCCORP, FCBRANCH, FCDEPT, FCSECT,
                FCJOB, FCSTEP, FDDATE, FCBOOK, FCCODE, FCREFNO, FNAMT, FCFRWHOUSE, FCTOWHOUSE,
                FCCREATEBY, FCCORRECTB, FMMEMDATA, FCEAFTERR, FCATSTEP, FTDATETIME, FCPROJ, FCCREATEAP
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $FCDATASER,
                $data['FCSKID'],
                $FCLUPDAPP,
                $data['FCRFTYPE'],
                $data['FCREFTYPE'],
                $FCCORP,
                $FCBRANCH,
                $data['FCDEPT'],
                $data['FCSECT'],
                $FCJOB,
                $FCSTEP,
                $data['FDDATE'],
                $data['FCBOOK'],
                $data['FCCODE'],
                $data['FCREFNO'],
                $data['FNAMT'],
                $data['FCFRWHOUSE'],
                $data['FCTOWHOUSE'],
                $data['FCCREATEBY'],
                $FCCORRECTB,
                $data['FMMEMDATA'],
                $FCEAFTERR,
                $FCATSTEP,
                $FTDATETIME,
                $FCPROJ,
                $FCCREATEAP
            ]
        );
    }

    public static function insertGlrefDataStore($data)
    {
        return DB::connection('itc_wms')->statement(
            'EXEC INSERT_TBL_GLREF ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
            [
                $data['FCSKID'],
                $data['FCRFTYPE'],
                $data['FCREFTYPE'],
                $data['FCDEPT'],
                $data['FCSECT'],
                $data['FDDATE'],
                $data['FCBOOK'],
                $data['FCCODE'],
                $data['FCREFNO'],
                $data['FNAMT'],
                $data['FCFRWHOUSE'],
                $data['FCTOWHOUSE'],
                $data['FCCREATEBY'],
                $data['FMMEMDATA']
            ]
        );
    }
}
