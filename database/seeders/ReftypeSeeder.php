<?php

namespace Database\Seeders;

use App\Models\FormulaReftype;
use App\Models\RefType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReftypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = FormulaReftype::all();
        foreach ($data as $item) {
            $FCSKID = trim($item->FCSKID);
            $FCCODE = trim($item->FCCODE);
            $FCRFTYPE = trim($item->FCRFTYPE);
            $FCNAME = trim($item->FCNAME);
            $FCNAME2 = trim($item->FCNAME2);
            $FCNGLNAME = trim($item->FCNGLNAME);
            $FCREPNAME = trim($item->FCREPNAME);

            RefType::updateOrCreate(
                ['FCSKID' => $FCSKID], // เงื่อนไขในการค้นหาข้อมูล
                [
                    'FCCODE' => $FCCODE,
                    'FCRFTYPE' => $FCRFTYPE,
                    'FCNAME' => $FCNAME,
                    'FCNAME2' => $FCNAME2,
                    'FCNGLNAME' => $FCNGLNAME,
                    'FCREPNAME' => $FCREPNAME,
                ]
            );
        }
    }
}
