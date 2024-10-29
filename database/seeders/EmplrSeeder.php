<?php

namespace Database\Seeders;

use App\Models\Emplr;
use App\Models\FormulaEmplr;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmplrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = FormulaEmplr::all();
        foreach ($data as $item) {
            $FCSKID = trim($item->FCSKID);
            $FCLOGIN = trim($item->FCLOGIN);
            $FCPW = trim($item->FCPW);
            $FCRCODE = trim($item->FCRCODE);

            Emplr::updateOrCreate(
                ['FCSKID' => $FCSKID], // เงื่อนไขในการค้นหาข้อมูล
                [
                    'FCLOGIN' => $FCLOGIN,
                    'FCPW' => $FCPW,
                    'FCRCODE' => $FCRCODE,
                ]
            );
        }
    }
}
