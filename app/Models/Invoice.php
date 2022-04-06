<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'company_id',
        'invoice_number',
        'expected_delivery',
        'payment_mode',
        'payment_term',
        'payment_partial_mode',
        'next_payment',
        'last_payment',
        'remarks'
    ];

    

    public function company() 
    {
        return $this->belongsTo(Company::class);
    } 


    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
