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
    ];

    public function stock()
    {
        return $this->belongsTo(PartStock::class, 'stock_id', 'id');
    }
}
