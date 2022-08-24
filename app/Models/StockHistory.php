<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_stock_id',
        'prev_unit_value',
        'current_unit_value',
        'type',
        'remarks',
    ];

    public function stock()
    {
        return $this->belongsTo(PartStock::class, 'part_stock_id', 'id');
    }
}
