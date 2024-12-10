<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function store(Request $request)
    {
        // รับค่า count จากคำขอ
        $currentCount = $request->input('count', 0); // ค่าเริ่มต้นคือ 0 หากไม่มีการส่งค่า count มา

        // เพิ่มค่า count
        $newCount = $currentCount + 1;

        // ส่งค่า count กลับไปยัง JavaScript
        return response()->json([
            'message' => 'Count updated successfully',
            'count' => $newCount
        ]);
    }
}
