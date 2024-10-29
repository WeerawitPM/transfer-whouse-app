<?php

namespace Database\Seeders;

use App\Models\Dept;
use App\Models\FormulaDept;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = FormulaDept::all();
        foreach ($data as $item) {
            $FCSKID = trim($item->FCSKID);
            $FCCORP = trim($item->FCCORP);
            $FCTYPE = trim($item->FCTYPE);
            $FCCODE = trim($item->FCCODE);
            $FCNAME = trim($item->FCNAME);
            $FCNAME2 = trim($item->FCNAME2);

            Dept::updateOrCreate(
                ['FCSKID' => $FCSKID], // เงื่อนไขในการค้นหาข้อมูล
                [
                    'FCCORP' => $FCCORP,
                    'FCTYPE' => $FCTYPE,
                    'FCCODE' => $FCCODE,
                    'FCNAME' => $FCNAME,
                    'FCNAME2' => $FCNAME2,
                ]
            );
        }
    }
}
