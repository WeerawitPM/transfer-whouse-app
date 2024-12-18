<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferBook extends Model
{
    protected $fillable = [
        'transfer_ref_type_id',
        'book_id',
        'is_active',
        'is_menu_scan',
        'is_menu_manual',
        'is_menu_detail',
    ];

    public function transfer_ref_type()
    {
        return $this->belongsTo(TransferRefType::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
