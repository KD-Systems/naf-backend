<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id','dn_number','remarks'];

    public function partItems()
    {
        return $this->morphMany(PartItem::class,'model');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
