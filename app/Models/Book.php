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
        'FROM_WHS',
        'TO_WHS',
    ];
}
