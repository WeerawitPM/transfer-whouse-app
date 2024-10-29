<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRefType extends Model
{
    protected $fillable = [
        'ref_type_id',
        'is_active',
    ];

    public function ref_type() {
        return $this->belongsTo(RefType::class);
    }
}
