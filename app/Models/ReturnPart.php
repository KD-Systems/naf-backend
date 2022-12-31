<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnPart extends Model
{
    use HasFactory;

    protected $table = 'return_parts';
    protected $fillable = [
        'tracking_number',
        'invoice_id',
        'grand_total'
    ];
    
}
