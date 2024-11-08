<?php

use App\Models\Book;
use App\Models\FormulaFormulas;
use App\Models\RefType;
use Illuminate\Support\Facades\DB;

function hadleSaveWrProduct($jobDetail, $user)
{
    $book = Book::where('FCREFTYPE', 'WR')->where('FCCODE', '0001')->get()->first();
    $book_fcskid = $book->FCSKID;
    $current_year = now()->year;
    $current_month = now()->format("m");
    $current_date = now()->toDateString();

    $FCCODE_GLREF = DB::connection('itc_wms')->select(
        'EXEC GET_FCCODE_GLREF ?, ?, ?',
        [$book_fcskid, $current_year, $current_month]
    );
    // dd($FCCODE[0]->FCCODE);

    $FCRFTYPE = RefType::where("FCSKID", $book->FCREFTYPE)->pluck("FCRFTYPE")->first();
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
        $fcseq_counter_mom++;

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
                $fcseq_counter_child++;
            }
        }
    }
}