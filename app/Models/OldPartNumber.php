<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldPartNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'part_number',
        'machine_id',
    ];

}
