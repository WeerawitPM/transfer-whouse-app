<?php

namespace App\Filament\Resources\TransferBookMenuResource\Functions;

use App\Models\Book;
use App\Models\Fccode_Glref;
use App\Models\FormulaFormulas;
use App\Models\Glref;
use App\Models\RefProd;
use App\Models\RefType;
use Illuminate\Support\Facades\DB;

class handleSaveWrProduct
{
    public static function handleSaveWrProduct($jobDetail, $user, $remark)
    {
        $book = Book::where('FCREFTYPE', 'WR')->where('FCCODE', '0001')->get()->first();
        $book_fcskid = $book->FCSKID;
        $current_year = now()->year;
        $current_month = now()->format("m");
        $current_date = now()->toDateString();

        $FCCODE_GLREF = Fccode_Glref::get_frcode_glref_store($book_fcskid, $current_year, $current_month);
        // dd($FCCODE_GLREF);

        $FCRFTYPE = RefType::where("FCSKID", $book->FCREFTYPE)->pluck("FCRFTYPE")->first();
        $FCREFTYPE = $book->FCREFTYPE;
        $FCDEPT = $user->dept->FCSKID;
        $FCSECT = $user->sect->FCSKID;
        $FDDATE = $current_date;
        $FCBOOK = $book_fcskid;
        $FCCODE = $FCCODE_GLREF;
        $FCREFNO = $book->FCPREFIX . $FCCODE_GLREF;
        $FCFRWHOUSE = $book->from_whs->FCSKID;
        $FCTOWHOUSE = $book->to_whs->FCSKID;
        $FCCREATEBY = $user->emplr->FCSKID;
        $FMMEMDATA = $remark;

        $fcseq_counter_mom = 01;

        foreach ($jobDetail as $item) {
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

            ////////////////////////////////////////////////////////////////////////////////////////////////////
            //REFPROD MOM
            $fcskid_formulas = FormulaFormulas::getChildProduct($item['part_no'])->get()->toArray();

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
                $FCSEQ = str_pad($fcseq_counter_mom, 2, "0", STR_PAD_LEFT);
                $FCPFORMULA = "$$$$$$$$";
                $FCFORMULAS = $fcskid_formulas[0]["CKEY"];
                $FCROOTSEQ = str_pad($fcseq_counter_mom, 2, "0", STR_PAD_LEFT);

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
            $fcseq_counter_mom++;

            ////////////////////////////////////////////////////////////////////////////
            //Child//
            $childProducts = [];
            // วนลูปเพื่อดึงค่า part_no แต่ละตัวใน jobDetail
            $partNo = $item['part_no'];
            // เรียกใช้ getChildProduct สำหรับแต่ละ part_no
            $childProduct = FormulaFormulas::getChildProduct($partNo)->get()->toArray();
            // เก็บผลลัพธ์ของแต่ละ part_no ไว้ใน array
            $childProducts[$partNo] = $childProduct;

            foreach ($childProducts as $child) {
                $fcseq_counter_child = 1;
                foreach ($child as $product) {
                    // dd($product);
                    for ($i = 0; $i < 2; $i++) {
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

                        $FCPROD = $product["KEY_SON"];
                        $FCREFPDTYP = "P";
                        $FCPRODTYPE = $product["FCTYPE"];
                        $FNQTY = $product["NQTY"] * $FNAMT;
                        $FNPRICE = $FNQTY * $product["FNSTDCOST"];
                        $FCUM = $product["UM_SON"];
                        $FCSEQ = $fcseq_counter_child;
                        $FCPFORMULA = $product["KEY_SON"];
                        $FCFORMULAS = "";
                        $FCROOTSEQ = $fcseq_counter_child;

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
                    $fcseq_counter_child++;
                }
            }
        }
        // dd("test");
        return;
    }
}