<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnPart extends Model
{
    use HasFactory;

    protected $table = 'return_parts';
    
    protected $fillable = [
        'invoice_id',
        'tracking_number',
        'created_by',
        'grand_total'
    ];

    public function returnPartItems()
    {
        return $this->hasMany(ReturnPartItem::class,'return_part_id','id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class,'id','invoice_id');
    }
    
}
