<?php

namespace Database\Seeders;

use App\Models\FormulaSect;
use App\Models\Sect;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = FormulaSect::all();
        foreach ($data as $item) {
            $FCSKID = trim($item->FCSKID);
            $FCCORP = trim($item->FCCORP);
            $FCDEPT = trim($item->FCDEPT);
            $FCCODE = trim($item->FCCODE);
            $FCNAME = trim($item->FCNAME);
            $FCNAME2 = trim($item->FCNAME2);

            Sect::updateOrCreate(
                ['FCSKID' => $FCSKID], // เงื่อนไขในการค้นหาข้อมูล
                [
                    'FCCORP' => $FCCORP,
                    'FCDEPT' => $FCDEPT,
                    'FCCODE' => $FCCODE,
                    'FCNAME' => $FCNAME,
                    'FCNAME2' => $FCNAME2,
                ]
            );
        }
    }
}
