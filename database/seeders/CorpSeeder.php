<?php

namespace Database\Seeders;

use App\Models\Corp;
use App\Models\FormulaCorp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CorpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = FormulaCorp::all();
        foreach ($data as $item) {
            $FCSKID = trim($item->FCSKID);
            $FCCODE = trim($item->FCCODE);
            $FCNAME = trim($item->FCNAME);
            $FCTAXID = trim($item->FCTAXID);
            $FCADDR1 = trim($item->FCADDR1);
            $FCADDR2 = trim($item->FCADDR2);
            $FCTEL = trim($item->FCTEL);
            $FCFAX = trim($item->FCFAX);

            Corp::updateOrCreate(
                ['FCSKID' => $FCSKID], // เงื่อนไขในการค้นหาข้อมูล
                [
                    'FCCODE' => $FCCODE,
                    'FCNAME' => $FCNAME,
                    'FCTAXID' => $FCTAXID,
                    'FCADDR1' => $FCADDR1,
                    'FCADDR2' => $FCADDR2,
                    'FCTEL' => $FCTEL,
                    'FCFAX' => $FCFAX,
                ]
            );
        }
    }
}
