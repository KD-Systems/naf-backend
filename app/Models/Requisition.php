<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'machine_id',
        'engineer_id',
        'priority',
        'type',
        'payment_mode',
        'expected_delivery',
        'payment_term',
        'payment_partial_mode',
        'partial_time',
        'next_payment',
        'ref_number',
        'machine_problems',
        'solutions',
        'reason_of_trouble',
        'remarks'
    ];


    public function partItems()
    {
        return $this->morphMany(PartItem::class,'partItemable');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
