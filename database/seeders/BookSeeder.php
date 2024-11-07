<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\FormulaBook;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = FormulaBook::all();
        foreach ($data as $item) {
            $FCSKID = trim($item->FCSKID);
            $FCREFTYPE = trim($item->FCREFTYPE);
            $FCCORP = trim($item->FCCORP);
            $FCBRANCH = trim($item->FCBRANCH);
            $FCCODE = trim($item->FCCODE);
            $FCNAME = trim($item->FCNAME);
            $FCNAME2 = trim($item->FCNAME2);
            $FCACCBOOK = trim($item->FCACCBOOK);
            $FCWHOUSE = trim($item->FCWHOUSE);
            $FCPREFIX = trim($item->FCPREFIX);

            Book::updateOrCreate(
                ['FCSKID' => $FCSKID], // เงื่อนไขในการค้นหาข้อมูล
                [
                    'FCREFTYPE' => $FCREFTYPE,
                    'FCCORP' => $FCCORP,
                    'FCBRANCH' => $FCBRANCH,
                    'FCCODE' => $FCCODE,
                    'FCNAME' => $FCNAME,
                    'FCNAME2' => $FCNAME2,
                    'FCACCBOOK' => $FCACCBOOK,
                    'FCWHOUSE' => $FCWHOUSE,
                    'FCPREFIX' => $FCPREFIX
                ]
            );
        }
    }
}
