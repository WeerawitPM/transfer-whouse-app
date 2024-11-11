<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RefProd extends Model
{
    protected $connection = 'itc_wms';
    protected $table = 'REFPROD';

    public static function insertRefProdData($data)
    {
        // Define constant values
        $FCDATASER = '$$$9';
        $FCLUPDAPP = '$0';
        $FCCORP = 'H2ZFEv02';
        $FCBRANCH = 'H2Z2kf01';
        $FCSTEP = 'I';
        $FCJOB = 'H2ZFfr02';
        $FCCORRECTB = '';
        $FCATSTEP = 'X';
        $FCCREATEAP = '';
        $FCPROJ = 'x/•ู((()';
        $FCEAFTERR = 'E';
        $FTDATETIME = Carbon::now(); // Current date and time
        $FIMILLISEC = '0';
        $FNXRATE = '1';
        $FNVATPORT = '0';
        $FNVATPORTA = '0';
        $FNCOSTAMT = '0';
        $FNPRICEKE = '0';
        $FMREMARK2 = '';
        $FCLOT = '';

        // Insert data into REFPROD table
        return DB::connection('itc_wms')->table('REFPROD')->insert([
            'FCDATASER' => $FCDATASER,
            'FCSKID' => $data['FCSKID'],
            'FCLUPDAPP' => $FCLUPDAPP,
            'FCCORP' => $FCCORP,
            'FCBRANCH' => $FCBRANCH,
            'FCDEPT' => $data['FCDEPT'],
            'FCSECT' => $data['FCSECT'],
            'FCJOB' => $FCJOB,
            'FCGLREF' => $data['FCGLREF'],
            'FDDATE' => $data['FDDATE'],
            'FCREFPDTYP' => $data['FCREFPDTYP'],
            'FCIOTYPE' => $data['FCIOTYPE'],
            'FCRFTYPE' => $data['FCRFTYPE'],
            'FCREFTYPE' => $data['FCREFTYPE'],
            'FCPRODTYPE' => $data['FCPRODTYPE'],
            'FCROOTSEQ' => $data['FCROOTSEQ'],
            'FCPFORMULA' => $data['FCPFORMULA'],
            'FCFORMULAS' => $data['FCFORMULAS'],
            'FCPROD' => $data['FCPROD'],
            'FCUM' => $data['FCUM'],
            'FCUMSTD' => $data['FCUM'],
            'FNUMQTY' => '1',
            'FNQTY' => $data['FNQTY'],
            'FCSTUM' => $data['FCUM'],
            'FCSTUMSTD' => $data['FCUM'],
            'FNSTUMQTY' => '1',
            'FNPRICE' => $data['FNPRICE'],
            'FCSEQ' => $data['FCSEQ'],
            'FCLOT' => $FCLOT,
            'FCWHOUSE' => $data['FCWHOUSE'],
            'FMREMARK2' => $FMREMARK2,
            'FCEAFTERR' => $FCEAFTERR,
            'FTDATETIME' => $FTDATETIME,
            'FIMILLISEC' => $FIMILLISEC,
            'FNVATPORT' => $FNVATPORT,
            'FNVATPORTA' => $FNVATPORTA,
            'FCPROJ' => $FCPROJ,
            'FNCOSTAMT' => $FNCOSTAMT,
            'FNPRICEKE' => $FNPRICEKE,
            'FNXRATE' => $FNXRATE,
            'FCCREATEAP' => $FCCREATEAP,
            'FCCREATEBY' => $data['FCCREATEBY'],
        ]);
    }

    public static function insertRefProdDataStore($data)
    {
        DB::connection('itc_wms')->statement(
            'EXEC INSERT_TBL_REFPROD ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
            [
                $data['FCSKID'], //FCSKID
                $data['FCRFTYPE'],
                $data['FCREFTYPE'],
                $data['FCDEPT'],
                $data['FCSECT'],
                $data['FDDATE'],
                $data['FCPROD'], //FCSKID ของ PROD
                $data['FCREFPDTYP'],
                $data['FCPRODTYPE'],
                $data['FNQTY'],
                $data['FNPRICE'],
                $data['FCUM'],
                $data['FCSEQ'],
                $data['FCIOTYPE'],
                $data['FCGLREF'], //FCGLREF
                $data['FCWHOUSE'],
                $data['FCCREATEBY'],
                $data['FMMEMDATA'],
                $data['FCPFORMULA'],
                $data['FCFORMULAS'],
                $data['FCROOTSEQ']
            ]
        );
    }
}
