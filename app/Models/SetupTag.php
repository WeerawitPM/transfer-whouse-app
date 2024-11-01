<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupTag extends Model
{
    protected $fillable = [
        'FCSKID',
        'FCCODE',
        'FCSNAME',
        'FCNAME',
        'packing_name',
        'packing_qty',
        'image',
    ];
}
