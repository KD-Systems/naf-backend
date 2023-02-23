<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistories extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id','payment_mode','transaction_details','file','payment_date','amount','remarks','created_by'];


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);  
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
