<?php

namespace App\Models;

use App\Traits\LogPreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdvancePaymentHistory extends Model
{
    use HasFactory, SoftDeletes, LogPreference;

    protected $fillable = [
        'company_id',
        'amount',
        'invoice_number',
        'payment_method',
        'payment_method',
        'payment_details',
        'transaction_type',
        'created_by',
        'updated_by',
        'remarks',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
