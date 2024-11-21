<?php

namespace App\Filament\Resources\TransferBookMenuResource\Functions;

use App\Models\Fccode_Glref;
use App\Models\Glref;
use App\Models\RefProd;
use App\Models\RefType;
use App\Models\Sect;
use Illuminate\Support\Facades\DB;

class handleSaveProduct
{
    public static function handleSaveProduct($jobDetail, $book, $user, $remark, $section)
    {
        $book_fcskid = $book->FCSKID;
        $current_year = now()->year;
        $current_month = now()->format("m");
        $current_date = now()->toDateString();
        $department = Sect::where("FCSKID", $section)->get()->first()->toArray();

        $FCCODE_GLREF = Fccode_Glref::get_frcode_glref_store($book_fcskid, $current_year, $current_month);
        // dd($FCCODE_GLREF);

        $FCRFTYPE = RefType::where("FCSKID", $book->FCREFTYPE)->pluck("FCRFTYPE")->first();
        // dd($FCRFTYPE);
        $FCREFTYPE = $book->FCREFTYPE;
        $FCDEPT = $department["FCDEPT"];
        $FCSECT = $section;
        $FDDATE = $current_date;
        $FCBOOK = $book_fcskid;
        $FCCODE = $FCCODE_GLREF;
        $FCREFNO = $book->FCPREFIX . $FCCODE_GLREF;
        $FCFRWHOUSE = $book->from_whs->FCSKID;
        $FCTOWHOUSE = $book->to_whs->FCSKID;
        $FCCREATEBY = $user->emplr->FCSKID;
        $FMMEMDATA = $remark;

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

            $data = [
                'FCSKID' => $FCSKID_GLREF,
                'FCRFTYPE' => $FCRFTYPE,
                'FCREFTYPE' => $FCREFTYPE,
                'FCDEPT' => $FCDEPT,
                'FCSECT' => $FCSECT,
                'FDDATE' => $FDDATE,
                'FCBOOK' => $FCBOOK,
                'FCCODE' => $FCCODE,
                'FCREFNO' => $FCREFNO,
                'FNAMT' => $FNAMT,
                'FCFRWHOUSE' => $FCFRWHOUSE,
                'FCTOWHOUSE' => $FCTOWHOUSE,
                'FCCREATEBY' => $FCCREATEBY,
                'FMMEMDATA' => $FMMEMDATA
            ];
            Glref::insertGlrefDataStore($data);
            // dd($data);

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

                $data = [
                    'FCSKID' => $FCSKID_REFPROD,
                    'FCRFTYPE' => $FCRFTYPE,
                    'FCREFTYPE' => $FCREFTYPE,
                    'FCDEPT' => $FCDEPT,
                    'FCSECT' => $FCSECT,
                    'FDDATE' => $FDDATE,
                    'FCPROD' => $FCPROD,
                    'FCREFPDTYP' => $FCREFPDTYP,
                    'FCPRODTYPE' => $FCPRODTYPE,
                    'FNQTY' => $FNQTY,
                    'FNPRICE' => $FNPRICE,
                    'FCUM' => $FCUM,
                    'FCSEQ' => $FCSEQ,
                    'FCIOTYPE' => $FCIOTYPE,
                    'FCGLREF' => $FCSKID_GLREF,
                    'FCWHOUSE' => $FCWHOUSE,
                    'FCCREATEBY' => $FCCREATEBY,
                    'FMMEMDATA' => $FMMEMDATA,
                    'FCPFORMULA' => $FCPFORMULA,
                    'FCFORMULAS' => $FCFORMULAS,
                    'FCROOTSEQ' => $FCROOTSEQ
                ];
                RefProd::insertRefProdDataStore($data);
            }
            $fcseq_counter++;
        }
        // dd("test");
        return;
    }
}
