<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaProd extends Model
{
    // ระบุการเชื่อมต่อฐานข้อมูล
    protected $connection = 'formula';

    // ชื่อของตารางที่ต้องการเชื่อมโยง
    protected $table = 'PROD';

    // ระบุฟิลด์ที่เป็น primary key
    protected $primaryKey = 'FCSKID';
    protected $keyType = 'string';

    // ถ้าตารางนี้ไม่มีฟิลด์ timestamps
    public $timestamps = false;

    protected $fillable = [
    ];
}
