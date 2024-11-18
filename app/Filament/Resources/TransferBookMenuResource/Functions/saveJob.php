<?php

namespace App\Filament\Resources\TransferBookMenuResource\Functions;

use App\Models\JobDetail;
use App\Models\JobToTag;
use App\Models\SetupTag;

class saveJob
{
    public static function saveJobToTag($job_id, $from_whs, $to_whs, $whouse, $user_id, $created_date, $data)
    {
        foreach ($data as $item) {
            $setupTag = SetupTag::where('FCCODE', $item['CPART_NO'])->first();
            if ($setupTag && $setupTag->image) {
                $item['image'] = asset('storage/' . $setupTag->image);
            } else {
                $item['image'] = asset('storage/image_part/error.jpg');
            }
            $kanban = explode(',', $item['KANBAN']);
            $qty = isset($kanban[1]) ? $kanban[1] : 0;
            $packing_name = isset($kanban[2]) ? $kanban[2] : 0;

            // dd($item);
            $jobToTag = JobToTag::create([
                'image' => $item['image'],
                'kanban' => $item['KANBAN'],
                'part_no' => $item['CPART_NO'],
                'part_code' => $item['FCSNAME'],
                'part_name' => $item['FCNAME'],
                'model' => $item['CMODEL'],
                'qty' => $qty,
                'packing_name' => $packing_name,
                'whouse' => $whouse,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'job_id' => $job_id,
                'created_date' => $created_date,
                'user_id' => $user_id,
            ]);
            $qr_code = $jobToTag->part_no . '@' . $jobToTag->qty . '@' . $jobToTag->packing_name . '@' . $jobToTag->whouse . '@' . $jobToTag->id;
            $jobToTag->qr_code = $qr_code;
            $jobToTag->save();
        }
    }

    public static function saveJobDetail($job_id, $from_whs, $to_whs, $whouse, $user_id, $created_date)
    {
        $data = JobToTag::where('job_id', $job_id)->get();
        $groupedData = $data->groupBy('part_no');

        foreach ($groupedData as $part_no => $items) {
            $image = $items->first()->image;
            $kanban = $items->first()->kanban;
            $part_code = $items->first()->part_code;
            $part_name = $items->first()->part_name;
            $model = $items->first()->model;
            $totalQty = $items->sum('qty');
            $packing_name = $items->first()->packing_name;

            JobDetail::create([
                'image' => $image,
                'kanban' => $kanban,
                'part_no' => $part_no,
                'part_code' => $part_code,
                'part_name' => $part_name,
                'model' => $model,
                'qty' => $totalQty,
                'packing_name' => $packing_name,
                'whouse' => $whouse,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'job_id' => $job_id,
                'created_date' => $created_date,
                'user_id' => $user_id,
            ]);
        }
    }

    public static function saveJobToTag_No_Kanban($job_id, $from_whs, $to_whs, $whouse, $user_id, $created_date, $data)
    {
        foreach ($data as $item) {
            $setupTag = SetupTag::where('FCCODE', $item['PART_NO'])->first();
            if ($setupTag && $setupTag->image) {
                $item['image'] = asset('storage/' . $setupTag->image);
            } else {
                $item['image'] = asset('storage/image_part/error.jpg');
            }

            // dd($item);
            $jobToTag = JobToTag::create([
                'image' => $item['image'],
                'part_no' => $item['PART_NO'],
                'part_code' => $item['PART_CODE'],
                'part_name' => $item['PART_NAME'],
                'model' => $item['MODEL'],
                'qty' => (int)$item['QTY'],
                'packing_name' => $item['UNIT'],
                'whouse' => $whouse,
                'from_whs' => $from_whs,
                'to_whs' => $to_whs,
                'status' => 0,
                'job_id' => $job_id,
                'created_date' => $created_date,
                'user_id' => $user_id,
            ]);
            $qr_code = $jobToTag->part_no . '@' . $jobToTag->qty . '@' . $jobToTag->packing_name . '@' . $jobToTag->whouse . '@' . $jobToTag->id;
            $jobToTag->qr_code = $qr_code;
            $jobToTag->save();
        }
    }
}
