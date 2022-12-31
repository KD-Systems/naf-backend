<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnPartItem extends Model
{
    use HasFactory;

    protected $table = 'return_part_items';
    protected $fillable = [
        'return_part_id',
        'part_id',
        'quanity',
        'unit_price',
        'total'
    ];
}
