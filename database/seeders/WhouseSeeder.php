<?php

namespace Database\Seeders;

use App\Models\FormulaWhouse;
use App\Models\Whouse;
use Illuminate\Database\Seeder;

class WhouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = FormulaWhouse::all();
        foreach ($data as $item) {
            $FCSKID = trim($item->FCSKID);
            $FCCORP = trim($item->FCCORP);
            $FCBRANCH = trim($item->FCBRANCH);
            $FCCODE = trim($item->FCCODE);
            $FCNAME = trim($item->FCNAME);

            Whouse::updateOrCreate(
                ['FCSKID' => $FCSKID], // เงื่อนไขในการค้นหาข้อมูล
                [
                    'FCCORP' => $FCCORP,
                    'FCBRANCH' => $FCBRANCH,
                    'FCCODE' => $FCCODE,
                    'FCNAME' => $FCNAME,
                ]
            );
        }
    }
}
