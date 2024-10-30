<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'FCSKID',
        'FCREFTYPE',
        'FCCORP',
        'FCBRANCH',
        'FCCODE',
        'FCNAME',
        'FCNAME2',
        'FCACCBOOK',
        'from_whs_id',
        'to_whs_id',
    ];

    public function from_whs()
    {
        return $this->belongsTo(Whouse::class);
    }

    public function to_whs()
    {
        return $this->belongsTo(Whouse::class);
    }
}
