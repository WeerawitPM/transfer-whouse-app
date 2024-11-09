<?php

namespace App\Filament\Resources\TransferBookMenuResource\Functions;

use App\Models\RefType;
use Illuminate\Support\Facades\DB;

class handleSaveProduct
{
    public static function handleSaveProduct($jobDetail, $book, $user)
    {
        // dd("test");
        $book_fcskid = $book->FCSKID;
        $current_year = now()->year;
        $current_month = now()->format("m");
        $current_date = now()->toDateString();

        $FCCODE_GLREF = DB::connection('itc_wms')->select(
            'EXEC GET_FCCODE_GLREF ?, ?, ?',
            [$book_fcskid, $current_year, $current_month]
        );
        // dd($FCCODE_GLREF[0]->FCCODE);

        $FCRFTYPE = RefType::where("FCSKID", $book->FCREFTYPE)->pluck("FCRFTYPE")->first();
        // dd($FCRFTYPE);
        $FCREFTYPE = $book->FCREFTYPE;
        $FCDEPT = $user->dept->FCSKID;
        $FCSECT = $user->sect->FCSKID;
        $FDDATE = $current_date;
        $FCBOOK = $book_fcskid;
        $FCCODE = $FCCODE_GLREF[0]->FCCODE;
        $FCREFNO = $book->FCPREFIX . $FCCODE_GLREF[0]->FCCODE;
        $FCFRWHOUSE = $book->from_whs->FCSKID;
        $FCTOWHOUSE = $book->to_whs->FCSKID;
        $FCCREATEBY = $user->emplr->FCSKID;
        $FMMEMDATA = "Scan";

        $fcseq_counter = 1;

        foreach ($jobDetail as $item) {
            // dd($item);
            do {
                $FCSKID_GLREF = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 7);

                // ตรวจสอบว่ามี $FCSKID อยู่ใน table GLREF หรือไม่
                $exists = DB::connection('itc_wms')->table('GLREF')
                    ->where('FCSKID', $FCSKID_GLREF)
                    ->exists();
            } while ($exists); // ถ้ามี $FCSKID_GLREF ซ้ำ จะสุ่มใหม่จนกว่าจะไม่ซ้ำ

            $FNAMT = $item['qty'];

            $INSERT_TBL_GLREF = DB::connection('itc_wms')->statement(
                'EXEC INSERT_TBL_GLREF ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                [
                    $FCSKID_GLREF,
                    $FCRFTYPE,
                    $FCREFTYPE,
                    $FCDEPT,
                    $FCSECT,
                    $FDDATE,
                    $FCBOOK,
                    $FCCODE,
                    $FCREFNO,
                    $FNAMT,
                    $FCFRWHOUSE,
                    $FCTOWHOUSE,
                    $FCCREATEBY,
                    $FMMEMDATA
                ]
            );

            ////////////////////////////////////////////////////////////////////////////////////////////////////
            //REFPROD
            for ($i = 0; $i < 2; $i++) {
                $product = DB::connection('formula')
                    ->table('PROD')
                    ->select('FCSKID', 'FCTYPE', 'FNSTDCOST', 'FCUM')
                    ->where('FCCODE', $item['part_no'])->get()->first();

                do {
                    $FCSKID_REFPROD = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 7);

                    // ตรวจสอบว่ามี $FCSKID อยู่ใน table REFPROD หรือไม่
                    $exists = DB::connection('itc_wms')->table('REFPROD')
                        ->where('FCSKID', $FCSKID_REFPROD)
                        ->exists();
                } while ($exists); // ถ้ามี $FCSKID ซ้ำ จะสุ่มใหม่จนกว่าจะไม่ซ้ำ

                if ($i == 0) {
                    $FCIOTYPE = "I";
                    $FCWHOUSE = $FCTOWHOUSE;
                } else {
                    $FCIOTYPE = "O";
                    $FCWHOUSE = $FCFRWHOUSE;
                }

                $FCPROD = $product->FCSKID;
                $FCREFPDTYP = "P";
                $FCPRODTYPE = $product->FCTYPE;
                $FNQTY = $FNAMT;
                $FNPRICE = $FNQTY * $product->FNSTDCOST;
                $FCUM = $product->FCUM;
                $FCSEQ = $fcseq_counter;
                $FCPFORMULA = "";
                $FCFORMULAS = "";
                $FCROOTSEQ = "";

                $INSERT_TBL_REFPROD = DB::connection('itc_wms')->statement(
                    'EXEC INSERT_TBL_REFPROD ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?',
                    [
                        $FCSKID_REFPROD, //FCSKID
                        $FCRFTYPE,
                        $FCREFTYPE,
                        $FCDEPT,
                        $FCSECT,
                        $FDDATE,
                        $FCPROD, //FCSKID ของ PROD
                        $FCREFPDTYP,
                        $FCPRODTYPE,
                        $FNQTY,
                        $FNPRICE,
                        $FCUM,
                        $FCSEQ,
                        $FCIOTYPE,
                        $FCSKID_GLREF, //FCGLREF
                        $FCWHOUSE,
                        $FCCREATEBY,
                        $FMMEMDATA,
                        $FCPFORMULA,
                        $FCFORMULAS,
                        $FCROOTSEQ
                    ]
                );
            }
            $fcseq_counter++;
        }
        return;
    }
}
